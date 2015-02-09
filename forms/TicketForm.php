<?php
/**
 * Created by JetBrains PhpStorm.
 * User: JanGolle
 * Date: 01.12.14
 * Time: 14:03
 * To change this template use File | Settings | File Templates.
 */

namespace app\forms;


use app\abstracts\BaseForm;
use app\models\Ticket;
use app\models\TicketMessage;
use Yii;

class TicketForm extends BaseForm
{
    public $category_id;
    public $name;
    public $text;

    public function rules()
    {
        return [
            [['category_id', 'name'], 'required'],
            [['name'], 'string', 'max' => 256, 'message' => Yii::t('error','Это поле не может превышать {count} символов',['count' => 256])],
            [['text'], 'string', 'max' => 2048, 'message' => Yii::t('error','Это поле не может превышать {count} символов',['count' => 2048])],
        ];
    }

    public function attributeLabels()
    {
        return [
            'category_id' => Yii::t('base', 'Категория'),
            'name' => Yii::t('base', 'Заголовок'),
            'text' => Yii::t('base', 'Текст'),
        ];
    }

    public function save()
    {
        if(!$this->validate())
            return false;

        $ticket = new Ticket();
        $ticket->setAttributes($this->getAttributes());
        $ticket->save(false);

        $ticketMessage = new TicketMessage();
        $ticketMessage->setAttributes([
            'text' => $this->text,
            'ticket_id' => $ticket->id
        ]);

        return $ticketMessage->save(false);
    }
}