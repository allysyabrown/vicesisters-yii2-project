<?php

namespace app\commands\DataTransfer;

use Yii;
use yii\db\ActiveRecord;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 17.11.2014
 * Time: 11:27
 */
class Model extends ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->get('db_mysql');
    }
}