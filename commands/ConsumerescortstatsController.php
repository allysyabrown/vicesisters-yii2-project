<?php
/**
 * Created by JetBrains PhpStorm.
 * User: JanGolle
 * Date: 25.11.14
 * Time: 15:49
 * To change this template use File | Settings | File Templates.
 */

namespace app\commands;


use PhpAmqpLib\Message\AMQPMessage;
use yii\console\Controller;

class ConsumerescortstatsController extends Controller {

    public function actionIndex()
    {
        $channel = \Yii::$app->amqp->channel;

        $channel->close();
    }

    public function actionViews()
    {
        $channel = \Yii::$app->amqp->channel;
        $channel->exchange_declare(ProducerescortstatsController::EXCHANGE_NAME,'direct',false,false,false);

        ProducerescortstatsController::addQueue(ProducerescortstatsController::QUEUE_VIEWS,ProducerescortstatsController::ROUTING_KEY_VIEWS);

        $dump = function(AMQPMessage $msg){
            $data = json_decode($msg->body);

            \Yii::$app->stats->escortViews($data->id)->dump($data->date,$data->amount);
            echo $data->id.PHP_EOL;
        };

        $channel->basic_consume(ProducerescortstatsController::QUEUE_VIEWS,'',false,true,false,false,$dump);

        echo '[*] Escort stats views consumer waiting for messages'.PHP_EOL;

        while(count($channel->callbacks)){
            $channel->wait();
        }

        $channel->close();
    }

    public function actionRating()
    {
        $channel = \Yii::$app->amqp->channel;
        $channel->exchange_declare(ProducerescortstatsController::EXCHANGE_NAME,'direct',false,false,false);

        ProducerescortstatsController::addQueue(ProducerescortstatsController::QUEUE_RATING,ProducerescortstatsController::ROUTING_KEY_RATING);

        $dump = function(AMQPMessage $msg){
            $data = json_decode($msg->body);

            \Yii::$app->stats->escortRating($data->id)->dump($data->date,$data->amount);
            echo $data->id.PHP_EOL;
        };

        $channel->basic_consume(ProducerescortstatsController::QUEUE_RATING,'',false,true,false,false,$dump);

        echo '[*] Escort stats rating consumer waiting for messages'.PHP_EOL;

        while(count($channel->callbacks)){
            $channel->wait();
        }

        $channel->close();
    }

    public function actionOnline()
    {
        $channel = \Yii::$app->amqp->channel;
        $channel->exchange_declare(ProducerescortstatsController::EXCHANGE_NAME,'direct',false,false,false);

        ProducerescortstatsController::addQueue(ProducerescortstatsController::QUEUE_ONLINE,ProducerescortstatsController::ROUTING_KEY_ONLINE);

        $dump = function(AMQPMessage $msg){
            $data = json_decode($msg->body);

            \Yii::$app->stats->escortOnline($data->id)->dump($data->date,$data->amount);
            echo $data->id.PHP_EOL;
        };

        $channel->basic_consume(ProducerescortstatsController::QUEUE_ONLINE,'',false,true,false,false,$dump);

        echo '[*] Escort stats online consumer waiting for messages'.PHP_EOL;

        while(count($channel->callbacks)){
            $channel->wait();
        }

        $channel->close();
    }

}