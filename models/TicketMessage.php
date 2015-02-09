<?php

namespace app\models;

use app\abstracts\BaseModel;
use app\components\FastData;
use Yii;

/**
 * This is the model class for table "ticket_message".
 *
 * @property integer $id
 * @property integer $from
 * @property string $text
 * @property string $date
 * @property string $extensions
 * @property integer $ticket_id
 * @property integer $status
 *
 * @property Ticket $ticket
 */
class TicketMessage extends BaseModel
{
    const STATUS_NEW = 1;
    const STATUS_READED = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ticket_message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from', 'text', 'date', 'ticket_id'], 'required'],
            [['from', 'ticket_id'], 'integer'],
            [['text', 'extensions'], 'string'],
            [['date'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from' => Yii::t('base','Автор'),
            'text' => Yii::t('base','Текст'),
            'date' => Yii::t('base','Дата'),
            'extensions' => Yii::t('base','Дополнения'),
            'ticket_id' => Yii::t('base','Тикет'),
        ];
    }

    public function setTicket($attributes)
    {
        $escortInfo = $this->setObjectAttributes(new Ticket(), $attributes);
        if($escortInfo !== null)
            $this->populateRelation('ticket', $escortInfo);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Ticket::className(), ['id' => 'ticket_id']);
    }

    public function beforeSave($insert)
    {
        if($this->date === null)
            $this->date = 'now';

        if($this->status === null)
            $this->status = self::STATUS_NEW;

        if($this->from === null)
            $this->from = Yii::$app->user->id;

        if(Yii::$app->user->getRole() === Account::ROLE_ADMIN){
            $ticketOwnerId = $this->ticket->account_id;
            $key = FastData::KEY_NEW_USER_TICKET . $ticketOwnerId;
        } else {
            $key = FastData::KEY_NEW_ADMIN_TICKET;
        }

        Yii::$app->fastData->addToString($key, $this->ticket_id);

        return parent::beforeSave($insert);
    }
}
