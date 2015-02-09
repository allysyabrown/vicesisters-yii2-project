<?php
/**
 * Created by PhpStorm.
 * User: rem
 * Date: 29.12.2014
 * Time: 11:50
 */

namespace app\repositories;

use Yii;
use app\models\Host;
use app\abstracts\Repository;

class HostRepository extends Repository
{
    public function findHost($id)
    {
        $query = Host::find()
                    ->where(['id' => $id]);

        return Yii::$app->dbCache->get($query, Yii::$app->params['maxCacheTime'])->one();
    }
}