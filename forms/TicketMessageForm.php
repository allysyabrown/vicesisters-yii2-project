<?php
/**
 * Created by JetBrains PhpStorm.
 * User: JanGolle
 * Date: 02.12.14
 * Time: 9:55
 * To change this template use File | Settings | File Templates.
 */

namespace app\forms;


use app\abstracts\BaseForm;
use app\models\TicketMessage;
use Yii;

class TicketMessageForm extends BaseForm {

    public $text;
    public $ticket_id;

    public function rules()
    {
        return [
            [['text'], 'string', 'max' => 2048, 'message' => Yii::t('error','Это поле не может превышать {count} символов',['count' => 2048])],
            [['ticket_id'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'ticket_id' => Yii::t('base', 'ID'),
            'text' => Yii::t('base', 'Текст'),
        ];
    }

    public function save()
    {
        if(!$this->validate())
            return false;

        if($this->ticket_id === null)
            return false;

        $ticketMessage = new TicketMessage();
        $ticketMessage->setAttributes([
            'text' => $this->text,
            'ticket_id' => $this->ticket_id,
        ]);

        return $ticketMessage->save(false);
    }

}