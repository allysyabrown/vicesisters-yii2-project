<?php

namespace app\models;

use app\behaviors\NotificationBehavior;
use Yii;
use app\abstracts\BaseModel;

/**
 * This is the model class for table "message".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $from
 * @property integer $to
 * @property string $text
 * @property string $date
 * @property string $extensions
 * @property string $dialog_code
 * @property integer $status
 *
 * @property bool $isMine
 *
 * @property \app\models\Account $author
 */
class Message extends BaseModel
{
    const PRIVATE_MESSAGE   = 1;
    const TICKET_MESSAGE    = 2;

    const STATUS_NEW        = 1;
    const STATUS_READED     = 0;
    const STATUS_DELETED    = 200;

    public $img;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'from', 'to', 'status'], 'integer'],
            [['from', 'to', 'text', 'status'], 'required'],
            [['text','dialog_code'], 'string'],
            [['date', 'extensions'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('base', 'ID'),
            'type' => Yii::t('base', 'Тип'),
            'from' => Yii::t('base', 'Автор'),
            'to' => Yii::t('base', 'Кому'),
            'text' => Yii::t('base', 'Текст'),
            'date' => Yii::t('base', 'Дата'),
            'extensions' => Yii::t('base', 'Дополнения'),
            'dialog_code' => Yii::t('base', 'Код диалога'),
            'status' => Yii::t('base', 'Статус'),
        ];
    }

    public function behaviors()
    {
        return [
            'notification' => NotificationBehavior::className(),
        ];
    }

    public function setAuthor($attributes)
    {
        $escortInfo = $this->setObjectAttributes(new Escort(), $attributes);
        if($escortInfo !== null)
            $this->populateRelation('author', $escortInfo);
    }

    /**
     * @return \app\models\Escort
     */
    public function getAuthor()
    {
        return $this->hasOne(Account::className(), ['id' => 'from']);
    }

    public function getNotifiedId()
    {
        return $this->to;
    }

    public function getIsNew()
    {
        return $this->status == self::STATUS_NEW && $this->from != Yii::$app->user->id;
    }

    public function read()
    {
        if($this->status === self::STATUS_NEW){
            $this->status = self::STATUS_READED;
            $this->update(true,['status']);
        }
    }

    /**
     * @return bool
     */
    public function getIsMine()
    {
        return $this->from == Yii::$app->user->id;
    }
}
