<?php
/**
 * Created by JetBrains PhpStorm.
 * User: JanGolle
 * Date: 20.11.14
 * Time: 15:25
 * To change this template use File | Settings | File Templates.
 */

namespace app\components;


use app\abstracts\Repository;
use app\repositories\StatsEscortViewsRepository;
use yii\base\Component;

class Stats extends Component {

    const KEY_ESCORT_STATS = 'escort-stats:';

    const FIELD_VIEWS = 'views';
    const FIELD_RATING = 'rating';
    const FIELD_ONLINE_TIME = 'online';

    /**
     * @var FastData
     */
    private $_fastData;

    /**
     * @var integer
     */
    private $_id;

    /**
     * @var string
     */
    private $_key;

    /**
     * @var string
     */
    private $_field;

    /**
     * @var string
     */
    private $_dbUserKey;

    /**
     * @var string | Repository | StatsEscortViewsRepository
     */
    private $_repository;

    public function init()
    {
        $this->_fastData = \Yii::$app->fastData;
    }

    public function escortViews($id)
    {
        $this->_id = $id;
        $this->_key = self::KEY_ESCORT_STATS.$id;
        $this->_field = self::FIELD_VIEWS;
        $this->_repository = \Yii::$app->data->getRepository('StatsEscortViews');
        $this->_dbUserKey = 'escort_id';

        return $this;
    }

    public function escortRating($id)
    {
        $this->_id = $id;
        $this->_key = self::KEY_ESCORT_STATS.$id;
        $this->_field = self::FIELD_RATING;
        $this->_repository = \Yii::$app->data->getRepository('StatsEscortRating');
        $this->_dbUserKey = 'escort_id';

        return $this;
    }

    public function escortOnline($id)
    {
        $this->_id = $id;
        $this->_key = self::KEY_ESCORT_STATS.$id;
        $this->_field = self::FIELD_ONLINE_TIME;
        $this->_repository = \Yii::$app->data->getRepository('StatsEscortOnline');
        $this->_dbUserKey = 'escort_id';

        return $this;
    }

    public function today()
    {
        return $this->all() - $this->_repository->getPeriodStats($this->_id);
    }

    public function all()
    {
        return (integer)$this->_fastData->hget($this->_key,$this->_field);
    }

    public function period($startDate, $endDate)
    {
        return (integer)$this->_repository->getPeriodStats($this->_id,$startDate,$endDate);
    }

    public function add($value = 1)
    {
        $this->_fastData->hincr($this->_key,$this->_field,$value);
    }

    public function dump($date = 'today',$amount = null)
    {
        if($amount === null)
            $amount = $this->today();

        $model = $this->_repository->getEntity();
        $model->setAttributes([
            'date' => $date,
            $this->_dbUserKey => $this->_id,
            'amount' => $amount
        ]);
        return $model->save();
    }

}