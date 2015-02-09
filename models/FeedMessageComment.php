<?php

namespace app\models;

use Yii;
use app\abstracts\BaseModel;
use app\behaviors\NotificationBehavior;

/**
 * This is the model class for table "feed_message_comment".
 *
 * @property integer $id
 * @property integer $feed_message_id
 * @property integer $owner_id
 * @property string $title
 * @property string $text
 * @property string $date
 *
 * @property integer $feedMessageId
 * @property integer $ownerId
 *
 * @property FeedMessage $feedMessage
 * @property Account $owner
 */
class FeedMessageComment extends BaseModel
{
    public $avatar;
    public $name;
    public $escortId;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'feed_message_comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['feed_message_id', 'owner_id', 'text', 'date'], 'required'],
            [['feed_message_id', 'owner_id'], 'integer'],
            [['title'], 'string', 'max' => 256],
            [['text'], 'string'],
            [['date', 'escortId', 'avatar', 'name'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'feed_message_id' => 'Feed Message ID',
            'owner_id' => 'User ID',
            'text' => 'Text',
            'date' => 'Date',
        ];
    }

    public function behaviors()
    {
        return [

        ];
    }

    public function setFeedMessage($attributes)
    {
        $escortInfo = $this->setObjectAttributes(new FeedMessage(), $attributes);
        if($escortInfo !== null)
            $this->populateRelation('feedMessage', $escortInfo);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedMessage()
    {
        return $this->hasOne(FeedMessage::className(), ['id' => 'feed_message_id']);
    }

    public function setOwner($attributes)
    {
        $escortInfo = $this->setObjectAttributes(new Account(), $attributes);
        if($escortInfo !== null)
            $this->populateRelation('owner', $escortInfo);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(Account::className(), ['id' => 'owner_id']);
    }

    public function beforeSave($insert)
    {
        if($this->getIsNewRecord()){
            if($this->getOwnerId() === null)
                $this->setOwnerId(Yii::$app->user->id);
            if($this->date === null)
                $this->date = Yii::$app->local->dateTime();
        }

        return parent::beforeSave($insert);
    }

    public function setFeedMessageId($id)
    {
        $this->feed_message_id = $id;
    }

    public function getFeedMessageId()
    {
        return $this->feed_message_id;
    }

    public function setOwnerId($id)
    {
        $this->owner_id = $id;
    }

    public function getOwnerId()
    {
        return $this->owner_id;
    }
}
