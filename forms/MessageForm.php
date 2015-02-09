<?php

namespace app\forms;

use app\abstracts\BaseModel;
use app\components\StaticData;
use app\models\Message;
use Yii;
use app\abstracts\BaseForm;

/**
 * Created by PhpStorm.
 * User: jangolle
 * Date: 05.12.2014
 * Time: 16:58
 */
class MessageForm extends BaseForm
{
//    public $id;
    public $type;
    public $from;
    public $to;
    public $text;
    public $date;
    public $extensions;
    public $dialog_code;
    public $status;

    private static $_hostId = 1;

    public function rules()
    {
        return [
            [['text'], 'required', 'message' => Yii::t('error', 'Это поле не может быть пустым')],
            [['text', 'date', 'dialog_code'], 'string', 'message' => Yii::t('error', 'Это текстовое поле')],
            [['date'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('base', 'ID'),
            'type' => Yii::t('base', 'Тип'),
            'from' => Yii::t('base', 'Автор'),
            'to' => Yii::t('base', 'Кому'),
            'text' => Yii::t('base', 'Текст вашего сообщения'),
            'extensions' => Yii::t('base', 'Дополнения'),
            'dialog_code' => Yii::t('base', 'Код диалога'),
        ];
    }

    public function hostId()
    {
        return static::$_hostId;
    }

    /**
     * @return bool|\app\models\Message
     */
    public function savePrivateMessage()
    {
        if($this->type === null)
            $this->type = Message::PRIVATE_MESSAGE;
        if($this->from === null)
            $this->from = Yii::$app->user->id;
        if($this->to === null)
            $this->to = Yii::$app->user->id;
        if($this->date === null)
            $this->date = (new \DateTime())->format(Yii::$app->params['dateTimeFormat']);
        if($this->dialog_code === null)
            $this->dialog_code = $this->createDialogCode($this->from,$this->to);
        if($this->status === null)
            $this->status = Message::STATUS_NEW;

        if(!$this->validate())
            return false;

        $attributes = $this->getAttributes();

        return Yii::$app->data->getRepository('Message')->createPrivateMessage($attributes);
    }

    private function createDialogCode($from,$to){
        if($from < $to)
            return $from.'-'.$to;

        return $to.'-'.$from;
    }
}