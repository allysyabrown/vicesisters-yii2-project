<?php
/**
 * Created by PhpStorm.
 * User: jangolle
 * Date: 15.12.2014
 * Time: 11:04
 */

namespace app\commands;


use app\abstracts\Producer;
use PhpAmqpLib\Channel\AMQPChannel;

class ProducerrecordsController extends Producer {

    const QUEUE_FEEDS = 'feeds';
    const QUEUE_COMMENTS = 'comments';

    const ROUTING_KEY_FEEDS = 'escort.records.feeds';
    const ROUTING_KEY_COMMENTS = 'escort.records.comments';

    const EXCHANGE_NAME = 'records';

    /**
     * @var AMQPChannel
     */
    private $_channel;

    public function actionIndex()
    {
        $this->_channel = \Yii::$app->amqp->channel;
        $this->_channel->exchange_declare(self::EXCHANGE_NAME,'direct',false,false,false);

        self::addQueue(self::QUEUE_FEEDS, self::ROUTING_KEY_FEEDS);
        self::addQueue(self::QUEUE_COMMENTS, self::ROUTING_KEY_COMMENTS);

        $this->dumpFeeds();
        $this->dumpComments();

        $this->_channel->close();
    }

    private function dumpFeeds()
    {
        $feeds = \Yii::$app->data->getRepository('FeedMessage')->getAllFeedsInFastData();

        if(!$feeds)
            return false;

        foreach($feeds as $feed){
            $feed = json_decode($feed);
            $this->sendMsg(['id' => $feed->id, 'escort_id' => $feed->escort_id], static::ROUTING_KEY_FEEDS, $this->_channel);
        }
    }

    private function dumpComments()
    {
        $comments = \Yii::$app->data->getRepository('FeedMessage')->getAllCommentsInFastData();

        if(!$comments)
            return false;

        foreach($comments as $comment){
            $comment = json_decode($comment);
            $this->sendMsg(['id' => $comment->id, 'feedMessageId' => $comment->feedMessageId], static::ROUTING_KEY_COMMENTS, $this->_channel);
        }
    }

} 