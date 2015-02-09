<?php

namespace app\repositories;

use Yii;
use app\models\User;
use app\models\Account;

/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 30.10.2014
 * Time: 21:44
 */
class UserRepository extends AccountRepository
{
    public function findEntityById($id)
    {
        if(!$account = $this->findCashedById($id, Account::className()))
            return null;

        $userClass = $this->entity->className();

        return $userClass::findCachedOne($id, $userClass);
    }

    public function findById($id)
    {
        $query = User::find()
                        ->where(['id' => $id]);

        return Yii::$app->dbCache->get($query, Yii::$app->params['accountCacheTime'])->one();
    }
}