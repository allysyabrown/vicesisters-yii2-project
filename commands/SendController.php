<?php
/**
 * Created by JetBrains PhpStorm.
 * User: JanGolle
 * Date: 18.11.14
 * Time: 13:54
 * To change this template use File | Settings | File Templates.
 */

namespace app\commands;


use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use yii\console\Controller;

class SendController extends Controller {

    public function actionIndex($msg = 'Hello, Vice!')
    {
        $channel = \Yii::$app->amqp->channel;
        $channel->queue_declare('hello', false, false, false, false);

//        $msg = new AMQPMessage($msg);
//        $channel->basic_publish($msg,'','hello');

        echo " [x] Sent 'Hello, Vice!'\n";

        $channel->close();
    }

    public function actionStats()
    {
        $channel = \Yii::$app->amqp->channel;
        $channel->exchange_declare(ConsumerEscortStatsController::EXCHANGE_NAME,'direct',false,false,false);

        $data = [
            'id' => 1501,
            'date' => '2014-11-28',
            'amount' => 182
        ];

        $this->sendMsg($data,$channel);

        $channel->close();
    }

    private function sendMsg($data,AMQPChannel $channel)
    {
        if(is_array($data))
            $data = json_encode($data);

        $msg = new AMQPMessage($data);
        $channel->basic_publish($msg,ConsumerEscortStatsController::EXCHANGE_NAME,ConsumerEscortStatsController::ROUTING_KEY);
    }

}