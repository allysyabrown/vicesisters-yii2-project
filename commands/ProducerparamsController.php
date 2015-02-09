<?php
/**
 * Created by PhpStorm.
 * User: jangolle
 * Date: 31.12.2014
 * Time: 10:45
 */

namespace app\commands;


use app\abstracts\Producer;
use app\models\Escort;
use PhpAmqpLib\Channel\AMQPChannel;

class ProducerparamsController extends Producer {

    const QUEUE_RATING = 'params_rating';

    const ROUTING_KEY_RATING = 'escort.params.rating';

    const EXCHANGE_NAME = 'params';

    /**
     * @var AMQPChannel
     */
    private $_channel;

    public function actionIndex()
    {
        $this->_channel = \Yii::$app->amqp->channel;
        $this->_channel->exchange_declare(self::EXCHANGE_NAME,'direct',false,false,false);

        self::addQueue(self::QUEUE_RATING,self::ROUTING_KEY_RATING);

        $this->dumpParams();

        $this->_channel->close();
    }

    private function dumpParams()
    {
        foreach(\Yii::$app->data->getRepository('Escort')->getIdAll() as $id){
            $this->dumpRating($id);
        }
    }

    private function dumpRating($escortId)
    {
        $rating = \Yii::$app->rating->get($escortId);

        $dbRating = Escort::findBySql('SELECT rating FROM escort WHERE id = :id', [':id' => $escortId])->one()->rating;

        if($rating != $dbRating && $rating){
            $data = [
                'id' => $escortId
            ];

            $this->sendMsg($data, self::ROUTING_KEY_RATING, $this->_channel);
        }
    }

} 