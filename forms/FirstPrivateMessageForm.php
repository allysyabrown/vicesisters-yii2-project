<?php

namespace app\forms;

use Yii;
use app\abstracts\BaseForm;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 19.12.2014
 * Time: 12:58
 */
class FirstPrivateMessageForm extends BaseForm
{
    public $id;
    public $from;
    public $to;
    public $text;
    public $date;

    public function rules()
    {
        return [
            [['text', 'from', 'to'], 'required'],
            [['from', 'to'], 'integer'],
            [['text'], 'string'],
            [['date'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'text' => Yii::t('front', 'Текст сообщения'),
            'date' => Yii::t('base', 'Дата'),
        ];
    }

    public function save()
    {
        Yii::$app->test->show($this);
    }
} 