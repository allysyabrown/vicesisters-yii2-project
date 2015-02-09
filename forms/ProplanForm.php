<?php

namespace app\forms;

use app\models\Membership;
use Yii;
use app\abstracts\BaseForm;
use yii\helpers\Json;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 24.11.2014
 * Time: 15:34
 */
class ProplanForm extends BaseForm
{
    public $id;
    public $userId;
    public $duration;
    public $price;
    public $rate;

    /**
     * @var \app\models\Membership
     */
    private $_proplan;

    /**
     * @var array
     */
    private $_durationItems;

    public function rules()
    {
        return [
            [['userId', 'duration'], 'required', 'message' => Yii::t('error', 'Это поле не может быть пустым')],
            [['price', 'rate'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('front', 'ID'),
            'userId' => Yii::t('front', 'ID пользователя'),
            'duration' => Yii::t('front', 'Длительность'),
        ];
    }

    /**
     * @param \app\models\Membership $proplan
     */
    public function setProplan($proplan)
    {
        $this->setAttributes($proplan->getAttributes());
        $this->_proplan = $proplan;
        $this->price = $proplan->getDiscountPrice($this->userId);
        $this->duration = (int)$this->duration;
    }

    public function getDurationItems()
    {
        if($this->_durationItems === null){
            $this->_durationItems = [];
            $options = Json::decode($this->getProplan()->durationOptions);
            if($options){
                $optionNames = ProplanDurationOptionsForm::getItems();

                foreach($options as $option){
                    $duration = (int)$option['duration'];
                    $this->_durationItems[$duration] = isset($optionNames[$duration]) ? $optionNames[$duration] : $duration;
                }
            }
        }
        return $this->_durationItems;
    }

    public function save()
    {
        if(!$this->validate())
            return false;

        $result = $this->model('Membership')->extendProplan($this);
        if($result && $this->rate)
            Yii::$app->rating->add($this->userId, $this->rate);

        return $result;
    }

    /**
     * @return \app\models\Membership
     */
    public function getProplan()
    {
        if($this->_proplan === null){
            $this->_proplan = $this->model('Membership')->findById($this->id);
        }
        return $this->_proplan;
    }
}