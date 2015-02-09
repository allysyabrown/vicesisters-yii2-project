<?php

namespace app\forms;

use Yii;
use app\abstracts\BaseForm;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 23.01.2015
 * Time: 12:46
 */
class ProplanDurationOptionsForm extends BaseForm
{
    public $duration;

    /**
     * @var array
     */
    private $_duraionItesms;

    /**
     * @return array
     */
    public function getDurationItems()
    {
        if($this->_duraionItesms === null){
            $this->_duraionItesms = self::getItems();
        }
        return $this->_duraionItesms;
    }

    public static function getItems()
    {
        return [
            1 => Yii::t('base', '1 час'),
            2 => Yii::t('base', '2 часа'),
            3 => Yii::t('base', '3 часа'),
            4 => Yii::t('base', '4 часа'),
            5 => Yii::t('base', '5 часов'),
            6 => Yii::t('base', '6 часов'),
            12 => Yii::t('base', '12 часов'),
            24 => Yii::t('base', '1 сутки'),
            48 => Yii::t('base', '2 суток'),
            72 => Yii::t('base', '3 суток'),
            120 => Yii::t('base', '5 суток'),
            168 => Yii::t('base', '1 неделя'),
            336 => Yii::t('base', '2 недели'),
            720 => Yii::t('base', '1 месяц'),
            1440 => Yii::t('base', '2 месяца'),
            2160 => Yii::t('base', '3 месяца'),
            4320 => Yii::t('base', '6 месяцев'),
            8640 => Yii::t('base', '1 год'),
        ];
    }
}