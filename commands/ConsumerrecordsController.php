<?php
/**
 * Created by PhpStorm.
 * User: jangolle
 * Date: 15.12.2014
 * Time: 13:05
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use PhpAmqpLib\Message\AMQPMessage;

class ConsumerrecordsController extends Controller {

    public function actionFeeds()
    {
        $channel = \Yii::$app->amqp->channel;
        $channel->exchange_declare(ProducerrecordsController::EXCHANGE_NAME,'direct',false,false,false);

        ProducerrecordsController::addQueue(ProducerrecordsController::QUEUE_FEEDS,ProducerrecordsController::ROUTING_KEY_FEEDS);

        $dump = function(AMQPMessage $msg){
            $data = json_decode($msg->body);

            if(\Yii::$app->data->getRepository('FeedMessage')->dumpFeed($data->id, $data->escort_id)) {
                echo '[success] - ' . $data->id . PHP_EOL;
            } else {
                echo '[error] - ' . $data->id . PHP_EOL;
            }
        };

        $channel->basic_consume(ProducerrecordsController::QUEUE_FEEDS,'',false,true,false,false,$dump);

        echo '[*] Feeds consumer waiting for messages'.PHP_EOL;

        while(count($channel->callbacks)){
            $channel->wait();
        }

        $channel->close();
    }

    public function actionComments()
    {
        $channel = \Yii::$app->amqp->channel;
        $channel->exchange_declare(ProducerrecordsController::EXCHANGE_NAME,'direct',false,false,false);

        ProducerrecordsController::addQueue(ProducerrecordsController::QUEUE_COMMENTS,ProducerrecordsController::ROUTING_KEY_COMMENTS);

        $dump = function(AMQPMessage $msg){
            $data = json_decode($msg->body);

            if(\Yii::$app->data->getRepository('FeedMessage')->dumpComment($data->id, $data->feedMessageId)) {
                echo '[success] - ' . $data->id . PHP_EOL;
            } else {
                echo '[error] - ' . $data->id . PHP_EOL;
            }
        };

        $channel->basic_consume(ProducerrecordsController::QUEUE_COMMENTS,'',false,true,false,false,$dump);

        echo '[*] Comments consumer waiting for messages'.PHP_EOL;

        while(count($channel->callbacks)){
            $channel->wait();
        }

        $channel->close();
    }

} 