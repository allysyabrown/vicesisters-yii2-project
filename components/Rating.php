<?php
/**
 * Created by JetBrains PhpStorm.
 * User: JanGolle
 * Date: 05.11.14
 * Time: 18:37
 * To change this template use File | Settings | File Templates.
 */

namespace app\components;

use app\models\Escort;
use Yii;
use yii\base\Component;
use yii\web\NotFoundHttpException;

class Rating extends Component
{
    /**
     * @var \app\components\FastData;
     */
    private $data;

    public function init(){
        $this->data = Yii::$app->fastData;
    }

    public function get($id){
        if($id === null)
            return false;

        return $this->data->hget(Stats::KEY_ESCORT_STATS.$id, Stats::FIELD_RATING);
    }

    public function set($id, $value){
        if($id != null){
            $this->data->hset(Stats::KEY_ESCORT_STATS.$id, Stats::FIELD_RATING, $value);
        }
    }

    public function add($id,$delta){
        $this->data->hincr(Stats::KEY_ESCORT_STATS.$id, Stats::FIELD_RATING, $delta);
    }

    public function save($id){
        if($id === null)
            return false;

        return Yii::$app->data->getRepository('Escort')->updateRating($id, $this->get($id));
    }
}