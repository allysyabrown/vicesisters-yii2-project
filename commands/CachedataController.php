<?php

namespace app\commands;

use Yii;
use yii\console\Controller;

/**
 * Created by PhpStorm.
 * User: Dima
 * Date: 05.01.2015
 * Time: 10:59
 */
class CachedataController extends Controller
{
    public function actionClear()
    {
        Yii::$app->cache->clear();

        echo "Cache cleared!\n";
        return true;
    }
}