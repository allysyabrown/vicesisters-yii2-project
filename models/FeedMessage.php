<?php

namespace app\models;

use Yii;
use app\behaviors\NotificationBehavior;
use app\abstracts\BaseModel;
use app\behaviors\LikeBehavior;

/**
 * This is the model class for table "feed_message".
 *
 * @property integer $id
 * @property integer $escort_id
 * @property integer $owner_id
 * @property string $text
 * @property string $date
 * @property string $title
 *
 * @property integer $escortId
 * @property integer $ownerId
 * @property integer $likes
 * @property integer $likesCount
 * @property bool $isLiked
 *
 * @property FeedMessageComment[] $comments
 * @property Escort $escort
 * @property Account $owner
 *
 * @property \app\behaviors\LikeBehavior $like
 * @property \app\behaviors\NotificationBehavior $notification
 */
class FeedMessage extends BaseModel
{
    public $avatar;
    public $name;
    public $likesCount;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'feed_message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['escort_id', 'owner_id', 'text', 'date'], 'required'],
            [['escort_id', 'owner_id'], 'integer'],
            [['title'], 'string', 'max' => 256],
            [['text'], 'string'],
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
            'escort_id' => 'Escort ID',
            'owner_id' => 'Owner ID',
            'text' => 'Text',
            'date' => 'Date',
        ];
    }

    public function behaviors()
    {
        return [
            'like' => LikeBehavior::className(),
            'notification' => NotificationBehavior::className(),
        ];
    }

    public function setComments($comments)
    {
        $records = $this->setObjectsAttributes(FeedMessageComment::className(), $comments);
        if($records !== null)
            $this->populateRelation('comments', $records);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(FeedMessageComment::className(), ['feed_message_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEscort()
    {
        return $this->hasOne(Escort::className(), ['id' => 'escort_id']);
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

    public function getLike()
    {
        return $this->getBehavior('like');
    }

    public function setEscortId($id)
    {
        $this->escort_id = $id;
    }

    public function getEscortId()
    {
        return $this->escort_id;
    }

    public function setOwnerId($id)
    {
        $this->owner_id = $id;
    }

    public function getOwnerId()
    {
        return $this->owner_id;
    }

    public function getLikes()
    {
        return $this->like->count();
    }

    public function getIsLiked()
    {
        return $this->like->isLiked();
    }

    public function getNotifiedId()
    {
        return $this->escort_id;
    }

    public function canLike()
    {
        return $this->ownerId != Yii::$app->user->id && !Yii::$app->user->isGuest;
    }

    public function canComment()
    {
        return !Yii::$app->user->isGuest;
    }

    public function getName()
    {
        return $this;
    }
}
