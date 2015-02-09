<?php

namespace app\commands;

use Yii;
use yii\base\Exception;
use yii\console\Controller;
use \Redis as RedisExt;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 10.12.2014
 * Time: 15:18
 */
class RedisConnectController extends Controller
{
    public function actionTest()
    {
        $yiiOk = 0;
        $yiiFail = 0;

        $baseOk = 0;
        $baseFail = 0;

        for($i = 0; $i < 20; $i++){
            try{
                $connection = (new RedisExt())->connect('redis_server', 6379);
                if($connection)
                    $baseOk++;
                else
                    $baseFail++;
            }catch(Exception $e){
                $baseFail++;
            }

            try{
                Yii::$app->redis->open();
                $connection = Yii::$app->redis->getIsActive();
                if($connection)
                    $yiiOk++;
                else
                    $yiiFail++;
            }catch(Exception $e){
                $yiiFail++;
            }
        }

        echo "stats:\r\n\r\n";

        echo "Base:\r\n";
        echo "ok: ".$baseOk."\r\n";
        echo "fail: ".$baseFail."\r\n\r\n";

        echo "Yii:\r\n";
        echo "ok: ".$yiiOk."\r\n";
        echo "fail: ".$yiiFail."\r\n\r\n";

        return true;
    }
} 