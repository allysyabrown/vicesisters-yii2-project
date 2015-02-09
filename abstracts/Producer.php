<?php
/**
 * Created by JetBrains PhpStorm.
 * User: JanGolle
 * Date: 27.11.14
 * Time: 16:16
 * To change this template use File | Settings | File Templates.
 */

namespace app\abstracts;


use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use yii\console\Controller;

abstract class Producer extends Controller {

    const EXCHANGE_NAME = 'abstract_exchange';

    protected function sendMsg($data, $routingKey, AMQPChannel $channel)
    {
        if(is_array($data))
            $data = json_encode($data);

        $msg = new AMQPMessage($data);
        $channel->basic_publish($msg, static::EXCHANGE_NAME, $routingKey);
    }

    public static function addQueue($name,$routingKey)
    {
        $channel = \Yii::$app->amqp->channel;
        $channel->queue_declare($name, false, false, false, false);
        $channel->queue_bind($name, static::EXCHANGE_NAME, $routingKey);
    }

}