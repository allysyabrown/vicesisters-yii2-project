<?php
/**
 * Created by JetBrains PhpStorm.
 * User: JanGolle
 * Date: 18.11.14
 * Time: 13:55
 * To change this template use File | Settings | File Templates.
 */

namespace app\commands;


use yii\console\Controller;

class ReceiveController extends Controller {

    public function actionIndex()
    {
        $channel = \Yii::$app->amqp->channel;
        $channel->queue_declare('hello', false, false, false, false);

        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

        $callback = function($msg) {
            echo " [x] Received ", $msg->body, "\n";
        };

        $channel->basic_consume('hello', '', false, true, false, false, $callback);

        while(count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
    }

}