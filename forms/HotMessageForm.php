<?php

namespace app\forms;

use app\abstracts\BaseModel;
use app\components\StaticData;
use app\models\Message;
use Yii;
use app\abstracts\BaseForm;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 19.11.2014
 * Time: 13:46
 */
class HotMessageForm extends BaseForm
{
    public $id;
    public $owner;
    public $title;
    public $text;
    public $date;
    public $img;

    private static $_hostId = 1;

    public function rules()
    {
        return [
            [['text', 'img'], 'required', 'message' => Yii::t('error', 'Это поле не может быть пустым')],
            [['text', 'date', 'img'], 'string', 'message' => Yii::t('error', 'Это текстовое поле')],
            [['title'], 'string', 'max' => 24, 'message' => Yii::t('error', 'Это поле не может первышать {count} символов', ['count' => 24])],
            [['text'], 'string', 'max' => 42, 'message' => Yii::t('error', 'Это поле не может превышать {count} символов', ['count' => 42])],
            //[['img'], 'file', 'skipOnEmpty' => true, 'message' => Yii::t('error', 'Загруженный файл не является изображением')],
            [['date', 'owner'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('base', 'ID'),
            'owner' => Yii::t('base', 'Автор'),
            'title' => Yii::t('base', 'Заголовок'),
            'text' => Yii::t('base', 'Текст'),
            'img' => Yii::t('base', 'Изображение'),
        ];
    }

    public function hostId()
    {
        return static::$_hostId;
    }

    public function addHotMessage()
    {
        if($this->owner === null)
            $this->owner = Yii::$app->user->id;
        if($this->date === null)
            $this->date = (new \DateTime())->format(Yii::$app->params['dateTimeFormat']);

        if(!$this->validate())
            return false;

        return $this->model('Message')->addHotMessage($this);
    }
}