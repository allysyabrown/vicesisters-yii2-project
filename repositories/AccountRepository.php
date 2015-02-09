<?php

namespace app\repositories;

use app\abstracts\Notification;
use app\components\FastData;
use app\forms\RegistrationForm;
use app\forms\UserAccountForm;
use app\models\Escort;
use app\models\EscortGeo;
use app\models\EscortInfo;
use app\models\EscortPhoto;
use app\models\MembershipDuration;
use app\models\User;
use Yii;
use app\abstracts\Repository;
use app\models\Account;
use app\forms\EscortAccountForm;
use yii\db\Transaction;
use app\models\Transaction as MoneyTransaction;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 12.11.14
 * Time: 9:16
 * @property \app\models\Account $entity
 */
class AccountRepository extends Repository
{
    /**
     * @param $name
     * @return null|Account
     */
    public function findByUsername($name)
    {
        $query = Account::find()
            ->where(['user_name' => $name]);

        return Yii::$app->dbCache->getOne($query, Yii::$app->params['accountCacheTime']);
    }

    /**
     * @param $id
     * @return \app\models\Account
     */
    public function findById($id)
    {
        $query = Account::find()
            ->where(['id' => $id]);

        return Yii::$app->dbCache->getOne($query, Yii::$app->params['accountCacheTime']);
    }

    public function findEntityByUserName($name)
    {
        if(!$account = $this->findByUsername($name))
            return null;

        if($account->role == Account::ROLE_ESCORT){
            $query = Escort::find()
                ->with('escortInfo')
                ->where(['email' => $name]);

            return Yii::$app->dbCache->getOne($query, Yii::$app->params['accountCacheTime']);
        }else{
            $class = $account->getUserClass();
            return $class::findCachedOne(['email', $this->entity->email]);
        }
    }

    public function findEntityById($id)
    {
        $account = $this->findById($id);
        if(!$account)
            return null;

        return Yii::$app->dbCache->getOne($this->getUserByIdQuery($account), Yii::$app->params['accountCacheTime']);
    }

    public function findEntity(Account $account)
    {
        $query = $this->getUserEntityQuery($account);
        if($query === null)
            return null;

        return Yii::$app->dbCache->getOne($query, Yii::$app->params['accountCacheTime']);
    }

    public function addUser(RegistrationForm $form)
    {
        $attributes = $form->getAttributes();
        $account = $this->entity;

        $account->setAttributes($attributes);

        $user = $account->getModel();

        $transaction = Yii::$app->db->beginTransaction();

        if(!$account->save(false))
            return $this->addUserFailed($form, $account, $transaction);

        $user->id = $account->id;
        $user->email = $account->user_name;
        $user->user_name = explode('@', $account->user_name)[0];

        $phone = isset($attributes['phone']) ? $attributes['phone'] : null;

        if($account->role === Account::ROLE_ESCORT){
            $info = new EscortInfo();
            $info->escort_id = $account->id;
            $info->phone = $phone;
            if(!$info->save(false))
                return $this->addUserFailed($form, $info, $transaction);

            $geo = new EscortGeo();
            $geo->setEscortId($account->id);
            if(!$geo->save(false))
                return $this->addUserFailed($form, $geo, $transaction);
        }else{
            $user->phone = $phone;
        }

        if(!$user->save(false))
            return $this->addUserFailed($form, $user, $transaction);

        $transaction->commit();
        return true;
    }

    public function saveEscortAccount(EscortAccountForm $form)
    {
        $userNotFoundError = Yii::t('error', 'Не удалось найти пользователя ID {id}', ['id' => $form->id]);
        $dbCache = Yii::$app->dbCache;

        $account = $this->findById($form->id);
        if(!$account){
            $form->addError(null, $userNotFoundError);
            return false;
        }

        $escortQuery = Escort::find()
            ->where(['id' => $form->id]);

        $escort = $dbCache->getOne($escortQuery, Yii::$app->params['accountCacheTime']);
        if(!$escort){
            $form->addError(null, $userNotFoundError);
            return false;
        }

        $escortInfoQuery = EscortInfo::find()
                                ->where(['escort_id' => $form->id]);
        $escortInfo = $dbCache->getOne($escortInfoQuery, Yii::$app->params['escortAccountCacheTime']);
        if(!$escortInfo){
            $form->addError(null, $userNotFoundError);
            return false;
        }

        $escortGeoQuery = EscortGeo::find()
            ->where(['escort_id' => $form->id]);

        $escortGeo = $dbCache->getOne($escortGeoQuery, Yii::$app->params['userGeoCacheTime']);
        if(!$escortGeo){
            $escortGeo = new EscortGeo();
            $escortGeo->setEscortId($form->id);
        }

        $attributes = $form->getAttributes();
        unset($attributes['password']);

        $attributes['city_id'] = $form->getGeoEntity()->city;
        $attributes['geo'] = json_encode($form->getGeoEntity());
        $attributes['body_params'] = json_encode($form->getBodyParamsEntity());
        $attributes['extended_info'] = $form->getExtendedInfo();

        $account->setAttributes($attributes);
        $account->userName = $form->email;
        $escort->setAttributes($attributes);
        $escortInfo->setAttributes($attributes);

        $geo = $form->getGeoEntity();
        $escortGeo->setCityId($geo->city);
        $escortGeo->setStateId($geo->state);
        $escortGeo->setCountryId($geo->country);
        $escortGeo->setRegionId($geo->region);
        $transaction = Yii::$app->db->beginTransaction();

        if(!$account->update(false))
            return $this->addUserFailed($form, $account, $transaction);
        if(!$escort->update(false))
            return $this->addUserFailed($form, $escort, $transaction);
        if(!$escortInfo->update(false))
            return $this->addUserFailed($form, $escortInfo, $transaction);
        if(!$escortGeo->save(false))
            return $this->addUserFailed($form, $escortInfo, $transaction);

        $transaction->commit();

        $accountQuery = Account::find()
            ->where(['id' => $form->id]);
        $dbCache->update($accountQuery, Yii::$app->params['accountCacheTime']);
        $dbCache->update($escortQuery, Yii::$app->params['escortAccountCacheTime']);
        $dbCache->update($escortInfoQuery, Yii::$app->params['escortAccountCacheTime']);
        $dbCache->update($escortGeoQuery, Yii::$app->params['userGeoCacheTime']);

        $query = Escort::find()
            ->with('escortInfo')
            ->with('city')
            ->with('country')
            ->with('photos')
            ->where(['id' => $form->id]);

        Yii::$app->dbCache->update($query, Yii::$app->params['escortAccountCacheTime']);

        $query = Escort::find()
            ->with(['escortInfo', 'photos'])
            ->where(['id' => $form->id]);

        Yii::$app->dbCache->update($query, Yii::$app->params['escortAccountCacheTime']);

        $query = $this->getUserEntityQuery($account);
        $dbCache->update($query, Yii::$app->params['accountCacheTime']);

        return true;
    }

    public function saveUserAccount(UserAccountForm $form)
    {
        $userNotFoundError = Yii::t('error', 'Не удалось найти пользователя ID {id}', ['id' => $form->id]);
        $dbCache = Yii::$app->dbCache;

        $account = $this->findById($form->id);
        if(!$account){
            $form->addError(null, $userNotFoundError);
            return false;
        }

        $userQuery = User::find()
                        ->where(['id' => $form->id]);
        $user = $dbCache->getOne($userQuery, Yii::$app->params['accountCacheTime']);
        if(!$user){
            $form->addError(null, $userNotFoundError);
            return false;
        }

        $attributes = $form->getAttributes();
        unset($attributes['password']);
        $attributes['info'] = json_encode($form->getInfoEntity());

        $account->setAttributes($attributes);
        $account->userName = $form->email;
        $user->setAttributes($attributes);

        $transaction = Yii::$app->db->beginTransaction();

        if(!$account->update(false))
            return $this->addUserFailed($form, $account, $transaction);
        if(!$user->update(false))
            return $this->addUserFailed($form, $user, $transaction);

        $transaction->commit();

        $accountQuery = Account::find()
            ->where(['id' => $form->id]);
        $dbCache->update($accountQuery, Yii::$app->params['accountCacheTime']);
        Yii::$app->dbCache->update($userQuery, Yii::$app->params['accountCacheTime']);
        $query = $this->getUserEntityQuery($account);
        $dbCache->update($query, Yii::$app->params['accountCacheTime']);

        return true;
    }

    public function setAmount($userId, $amount, MoneyTransaction $moneyTransaction)
    {
        $account = Account::findCachedOne($userId);
        if(!$account)
            return false;

        $account->balance = $amount;

        $transaction = Yii::$app->db->beginTransaction();

        if(!$this->cmd()->update($account->tableName(), ['balance' => $amount], ['id' => $userId])->execute()){
            $transaction->rollBack();
            return false;
        }

        if(!$moneyTransaction->save(false)){
            $transaction->rollBack();
            return false;
        }

        $transaction->commit();

        $query = Account::find()
            ->where(['id' => $userId]);

        Yii::$app->dbCache->update($query);

        return true;
    }

    public function addMoneyToBalance($userId, MoneyTransaction $moneyTransaction)
    {
        $sum = $moneyTransaction->sum;
        $account = $this->findById($userId);
        if(!$account)
            return false;

        $account->balance += $sum;

        $transaction = Yii::$app->db->beginTransaction();

        if(!$this->cmd()->update($account->tableName(), ['balance' => $account->balance], ['id' => $userId])->execute()){
            $transaction->rollBack();
            return false;
        }

        $moneyTransaction->escort_balance = $account->balance;
        if(!$moneyTransaction->save(false)){
            $transaction->rollBack();
            return false;
        }

        $transaction->commit();

        $query = Account::find()
            ->where(['id' => $userId]);

        Yii::$app->dbCache->update($query);

        return $account->balance;

    }

    public function setPassword($userId, $newPassword)
    {
        $account = Account::findCachedOne($userId);
        if(!$account)
            return false;

        $account->password = $newPassword;

        $result = $this->cmd()->update($account->tableName(), ['password' => $newPassword], ['id' => $userId])->execute();
        if($result){
            Yii::$app->dbCache->updateById($account, $userId);
            Yii::$app->user->getIdentity()->password = $newPassword;
        }

        return $result;
    }

    public function setAvatar($data)
    {
        $user = Yii::$app->user->getEntity();
        if(!$user)
            return false;

        $image = Yii::$app->image->uploadImageFromSourceNoHost($data);
        if(!$image)
            return false;

        $image = trim($image);
        $user->avatar = $image;

        $result = $user->update(false, ['avatar']);

        if($result){
            $account = Yii::$app->user->getIdentity();

            Yii::$app->dbCache->update($this->getUserEntityQuery($account), Yii::$app->params['accountCacheTime']);
            Yii::$app->dbCache->update($this->getUserByIdQuery($account), Yii::$app->params['accountCacheTime']);
        }

        return $result;
    }

    /**
     * @param \app\abstracts\BaseForm $form
     * @param \app\abstracts\BaseModel $model
     * @param \yii\db\Transaction $transaction
     * @return bool
     */
    private function addUserFailed($form, $model, $transaction = null)
    {
        $form->setErrors($model->errors);
        if($transaction !== null)
            $transaction->rollBack();
        return false;
    }

    /**
     * @return Account[] | null
     */
    public function getFavorites()
    {
        $this->entity = $this->findById(Yii::$app->user->id);
        $favList = $this->getPgArray($this->entity->favorites);

        $favs = [];

        foreach($favList as $fav){
            $favs[] = Yii::$app->data->getRepository('Account')->findCashedById($fav);
        }

        return $favs;
    }

    public function getOtherLikesMe()
    {
        $sql = 'SELECT id FROM account WHERE favorites @> ARRAY[:id]::int[]';
        $favList = Account::findBySql($sql,[':id' => Yii::$app->user->id])->all();

        $favs = [];

        foreach($favList as $fav){
            $favs[] = Yii::$app->data->getRepository('Account')->findCashedById($fav->id);
        }

        return $favs;
    }

    public function addFavorite($id)
    {
        $this->entity = $this->findById(Yii::$app->user->id);
        if ($this->entity->favorites = $this->addToPgArray($this->entity->favorites, $id)){
            Notification::addAnswerFavorite($id);
            return (bool)$this->entity->update(true, ['favorites']);
        } else {
            return false;
        }
    }

    public function removeFavorite($id)
    {
        $this->entity = $this->findById(Yii::$app->user->id);
        if($this->entity->favorites = $this->removeFromPgArray($this->entity->favorites, $id))
            return (bool)$this->entity->update(true,['favorites']);

        return false;
    }

    /**
     * @param Account $account
     * @return \yii\db\ActiveQuery|null
     */
    public function getUserEntityQuery(Account $account)
    {
        if($account->getRole() === Account::ROLE_ESCORT || $account->getRole() === Account::ROLE_VERIFIED_ESCORT){
            $query = Escort::find()->with('escortInfo');
            if($account->id)
                $query->where(['id' => $account->id]);
            elseif($account->user_name)
                $query->where(['email' => $account->user_name]);
            else
                return null;
        }elseif($account->getRole() === Account::ROLE_USER){
            $query = User::find();
            if($account->id)
                $query->where(['id' => $account->id]);
            elseif($account->user_name)
                $query->where(['email' => $account->user_name]);
            else
                return null;
        }else{
            $query = Account::find();
            if($account->id)
                $query->where(['id' => $account->id]);
            elseif($account->user_name)
                $query->where(['user_name' => $account->user_name]);
            else
                return null;
        }

        return $query;
    }

    /**
     * @param Account $account
     * @return \yii\db\ActiveQuery|null
     */
    public function getUserByIdQuery(Account $account)
    {
        if($account->getIsEscort()){
            $query = Escort::find()
                ->with('escortInfo')
                ->where(['id' => $account->id]);
        }else{
            $query = User::find()
                ->where(['id' => $account->id]);
        }

        return $query;
    }

    public function remove(Account $account)
    {
        if($account->getRole() == Account::ROLE_ADMIN){
            $account->addError(null, Yii::t('back', 'Невозможно удалить администратора!'));
            return false;
        }

        $db = Yii::$app->getDb();

        $transaction = $db->beginTransaction();

        $result = Account::deleteAll(['id' => $account->id]);

        if(!$result){
            $transaction->rollBack();
            $account->addError(null, Yii::t('back', 'Не удалось удалить аккаунт ID {id}', ['id' => $account->id]));
            return false;
        }

        if($account->getRole() == Account::ROLE_USER){
            $result = User::deleteAll(['id' => $account->id]);
            if(!$result){
                $transaction->rollBack();
                $account->addError(null, Yii::t('back', 'Не удалось удалить пользовоателя ID {id}', ['id' => $account->id]));
                return false;
            }
        }else{
            $result = Escort::deleteAll(['id' => $account->id]);
            if(!$result){
                $transaction->rollBack();
                $account->addError(null, Yii::t('back', 'Не удалось удалить пользовоателя ID {id}', ['id' => $account->id]));
                return false;
            }
            $result = EscortInfo::deleteAll(['escort_id' => $account->id]);
            if(!$result){
                $transaction->rollBack();
                $account->addError(null, Yii::t('back', 'Не удалось удалить информацию о пользователе ID {id}', ['id' => $account->id]));
                return false;
            }
            $result = EscortPhoto::deleteAll(['escort_id' => $account->id]);
            if(!$result){
                $transaction->rollBack();
                $account->addError(null, Yii::t('back', 'Не удалось фотограФии пользователя'));
                return false;
            }
            $result = MembershipDuration::deleteAll(['escort_id' => $account->id]);
            if(!$result){
                $transaction->rollBack();
                $account->addError(null, Yii::t('back', 'Не удалось удалить информацию о членстве пользователя'));
                return false;
            }
        }

        $transaction->commit();
        return true;
    }
}