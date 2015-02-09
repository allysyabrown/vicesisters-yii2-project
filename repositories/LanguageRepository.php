<?php

namespace app\repositories;

use app\models\Language;
use Yii;
use app\abstracts\Repository;


/**
 * Created by PhpStorm.
 * User: rem
 * Date: 12.12.2014
 * Time: 18:25
 */
class LanguageRepository extends Repository
{
    public function findById($id)
    {
        $query = Language::find()
                        ->where(['id' => $id]);

        return Yii::$app->dbCache->get($query, Yii::$app->params['maxCacheTime'])->asArray()->one();
    }

    public function all()
    {
        $query = Language::find()
                    ->select(['id', 'name'])
                    ->orderBy('name');

        return Yii::$app->dbCache->get($query, Yii::$app->params['maxCacheTime'])->asArray()->all();
    }
}