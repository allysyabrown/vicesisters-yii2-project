<?php
/**
 * Created by JetBrains PhpStorm.
 * User: JanGolle
 * Date: 26.11.14
 * Time: 12:11
 * To change this template use File | Settings | File Templates.
 */

namespace app\commands;


use app\abstracts\Producer;
use PhpAmqpLib\Channel\AMQPChannel;

class ProducerescortstatsController extends Producer {

    const QUEUE_VIEWS = 'escort_views';
    const QUEUE_RATING = 'escort_rating';
    const QUEUE_ONLINE = 'escort_online';

    const ROUTING_KEY_VIEWS = 'escort.stats.views';
    const ROUTING_KEY_RATING = 'escort.stats.rating';
    const ROUTING_KEY_ONLINE = 'escort.stats.online';

    const EXCHANGE_NAME = 'stats';

    /**
     * @var AMQPChannel
     */
    private $_channel;

    public function actionIndex()
    {
        $this->_channel = \Yii::$app->amqp->channel;
        $this->_channel->exchange_declare(self::EXCHANGE_NAME,'direct',false,false,false);

        self::addQueue(self::QUEUE_VIEWS,self::ROUTING_KEY_VIEWS);
        self::addQueue(self::QUEUE_RATING,self::ROUTING_KEY_RATING);
        self::addQueue(self::QUEUE_ONLINE,self::ROUTING_KEY_ONLINE);

        $this->dumpStats();

        $this->_channel->close();
    }

    private function dumpStats()
    {
        $today = new \DateTime();
        $today = $today->format(\Yii::$app->params['dateFormat']);

        foreach(\Yii::$app->data->getRepository('Escort')->getIdAll() as $id){
            $this->dumpViews($id,$today);
            $this->dumpRating($id,$today);
            $this->dumpOnline($id,$today);
        }
    }

    private function dumpViews($escortId,$date)
    {
        $views = \Yii::$app->stats->escortViews($escortId)->today();

        if($views > 0){
            $data = [
                'id' => $escortId,
                'date' => $date,
                'amount' => $views
            ];

            $this->sendMsg($data, self::ROUTING_KEY_VIEWS, $this->_channel);
        }
    }

    private function dumpRating($escortId,$date)
    {
        $rating = \Yii::$app->stats->escortViews($escortId)->today();

        if($rating > 0){
            $data = [
                'id' => $escortId,
                'date' => $date,
                'amount' => $rating
            ];

            $this->sendMsg($data, self::ROUTING_KEY_RATING, $this->_channel);
        }
    }

    private function dumpOnline($escortId,$date)
    {
        $online = \Yii::$app->stats->escortViews($escortId)->today();

        if($online > 0){
            $data = [
                'id' => $escortId,
                'date' => $date,
                'amount' => $online
            ];

            $this->sendMsg($data, self::ROUTING_KEY_ONLINE, $this->_channel);
        }
    }
}