<?php
/**
 * Created by PhpStorm.
 * User: rem
 * Date: 26.12.2014
 * Time: 11:35
 */

namespace app\repositories;

use Yii;
use app\forms\EditProlanForm;
use app\models\Transaction;
use app\forms\ProplanForm;
use app\models\MembershipDuration;
use app\abstracts\Repository;
use app\models\Membership;

class MembershipRepository extends Repository
{
    public function findById($id)
    {
        $query = Membership::find()
            ->where(['id' => $id]);

        return Yii::$app->dbCache->get($query, Yii::$app->params['maxCacheTime'])->one();
    }

    public function getProplans()
    {
        $query = Membership::find();
        $query->orderBy('id DESC');

        return Yii::$app->dbCache->get($query, Yii::$app->params['maxCacheTime'])->all();
    }

    public function getMembersIdList($id)
    {
        $query = MembershipDuration::find()
                    ->select('escort_id')
                    ->where(['membership_id' => $id])
                    ->andWhere(['>=', 'end_date', 'now']);

        $ids = [];

        $result = Yii::$app->dbCache->get($query, Yii::$app->params['proplanMembersCacheTime'])->asArray()->all();

        if($result){
            foreach($result as $res){
                $ids[] = $res['escort_id'];
            }
        }

        return $ids;
    }

    public function getProplansList()
    {
        $query = Membership::find()->orderBy('id');

        return Yii::$app->dbCache->get($query, Yii::$app->params['proplansCacheTime'])->all();
    }

    public function findByUserId($userId)
    {
        $query = MembershipDuration::find()
            ->where(['escort_id' => $userId])
            ->andWhere(['>=', 'end_date', 'now']);

        return Yii::$app->dbCache->get($query, Yii::$app->params['escortAccountCacheTime'])->all();
    }

    public function extendProplan(ProplanForm $form)
    {
        $proplan = $this->findById($form->id);
        if(!$proplan){
            $form->addError(null, Yii::t('error', 'Не удалось найти account ID {id}', ['id' => $form->id]));
            return false;
        }

        $user = Yii::$app->data->getRepository('Account')->findById($form->userId);
        if(!$user){
            $form->addError(null, Yii::t('error', 'Пользователь ID {id} не найден', ['id' => $form->id]));
            return false;
        }

        $sum = $form->price*($form->duration/$form->getProplan()->duration);
        $balance = $user->balance - $sum;
        if($balance < 0){
            $form->addError(null, Yii::t('error', 'Недостаточно денег на счету'));
            return false;
        }

        $duration = MembershipDuration::find()
            ->where(['membership_id' => $form->id])
            ->andWhere(['escort_id' => $form->userId])
            ->andWhere(['>=', 'end_date', 'now'])
            ->one();

        $local = Yii::$app->local;

        if($duration){
            $duration->setEndDate($local->addHoursToDate($duration->getEndDate(), $form->duration));

            $result = $duration->update(false, ['end_date']);
        }else{
            $duration = new MembershipDuration();
            $duration->setEscortId($form->userId);
            $duration->setMembershipId($form->id);
            $duration->setStartDate($local->dateTime());
            $duration->setEndDate($local->addHoursToDate($duration->getStartDate(), $form->duration));

            $result = $duration->save(false);
        }

        if($result){
            $serviceName = trim($duration->membership->name);

            $transaction = new Transaction();
            $transaction->setEscortId($form->userId);
            $transaction->setServiceName($serviceName);
            $transaction->sum = $sum;
            $transaction->setEscortBalance($balance);
            $transaction->description = Yii::t('front', 'Оплата за подключение услуги "{proplan}"', ['proplan' => $serviceName]);

            $result = Yii::$app->data->getRepository('Account')->setAmount($form->userId, $balance, $transaction);
        }

        if($result)
            return $duration->getEndDate();
        else
            return false;
    }

    public function editProplan(EditProlanForm $form)
    {
        $proplan = $this->findById($form->id);
        if(!$proplan){
            $form->addError(null, Yii::t('error', 'Не удалось найти проплан ID {id}', ['id' => $form->id]));
            return false;
        }

        $proplan->setAttributes($form->getAttributes());

        $result = Yii::$app->getDb()->createCommand()->update(Membership::tableName(), $proplan->getAttributes(), ['id' => $form->id])->execute();
        if($result){
            $query = Membership::find()
                ->where(['id' => $form->id]);

            Yii::$app->dbCache->update($query, Yii::$app->params['maxCacheTime']);
        }else{
            $form->addError(null, Yii::t('error', 'Не удалось найти сохранить проплан'));
        }

        return (bool)$result;
    }
} 