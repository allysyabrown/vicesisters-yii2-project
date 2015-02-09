<?php

namespace app\repositories;

use app\forms\EscortTravelsForm;
use app\models\EscortInfo;
use Yii;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 20.11.2014
 * Time: 18:36
 */
class EscortInfoRepository extends AccountRepository
{
    public function setTravelList(EscortTravelsForm $form)
    {
        $result = $this->cmd()->update($this->entity->tableName(), ['geo' => json_encode($form->getGeoEntity())], ['id' => $form->id])->execute();
        if($result){
            $query = Yii::$app->data->getRepository('Account')->getUserEntityQuery(Yii::$app->user->getIdentity());
            if($query !== null)
                Yii::$app->dbCache->update($query, Yii::$app->params['accountCacheTime']);
        }

        return $result;
    }
} 