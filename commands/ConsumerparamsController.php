<?php
/**
 * Created by PhpStorm.
 * User: jangolle
 * Date: 31.12.2014
 * Time: 10:59
 */

namespace app\commands;


use PhpAmqpLib\Message\AMQPMessage;
use yii\console\Controller;

class ConsumerparamsController extends Controller {

    public function actionRating()
    {
        $channel = \Yii::$app->amqp->channel;
        $channel->exchange_declare(ProducerparamsController::EXCHANGE_NAME,'direct',false,false,false);

        ProducerparamsController::addQueue(ProducerparamsController::QUEUE_RATING,ProducerparamsController::ROUTING_KEY_RATING);

        $dump = function(AMQPMessage $msg){
            $data = json_decode($msg->body);

            \Yii::$app->rating->save($data->id);
            echo $data->id.PHP_EOL;
        };

        $channel->basic_consume(ProducerparamsController::QUEUE_RATING,'',false,true,false,false,$dump);

        echo '[*] Escort params rating consumer waiting for messages'.PHP_EOL;

        while(count($channel->callbacks)){
            $channel->wait();
        }

        $channel->close();
    }

}
