<?php

namespace app\models;

use Yii;
use app\helpers\Like;
use app\abstracts\BaseModel;
use app\behaviors\LikeBehavior;
use app\components\FastData;

/**
 * This is the model class for table "escort_photo".
 *
 * @property string $id
 * @property string $escort_id
 * @property integer $host_id
 * @property string $path
 * @property integer $verified
 *
 * @property string $src
 *
 * @property Host $host
 * @property Escort $escort
 *
 * @property \app\behaviors\LikeBehavior $like
 */
class EscortPhoto extends BaseModel
{
    const NOT_VERIFIED = 0;
    const VERIFIED = 1;

    private $_likes;
    private $_isLiked;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'escort_photo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['escort_id', 'host_id'], 'required'],
            [['escort_id', 'host_id'], 'integer'],
            [['path'], 'string', 'max' => 128],
            [['verified'], 'safe'],
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
            'host_id' => 'Host ID',
            'path' => 'Path',
            'verified' => 'Verified',
        ];
    }

    public function behaviors()
    {
        return [
            'like' => LikeBehavior::className(),
        ];
    }

    public function setHost($attributes)
    {
        $escortInfo = $this->setObjectAttributes(new Host(), $attributes);
        if($escortInfo !== null)
            $this->populateRelation('host', $escortInfo);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHost()
    {
        return $this->hasOne(Host::className(), ['id' => 'host_id']);
    }

    public function setEscort($attributes)
    {
        $escortInfo = $this->setObjectAttributes(new Escort(), $attributes);
        if($escortInfo !== null)
            $this->populateRelation('escort', $escortInfo);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEscort()
    {
        return $this->hasOne(Escort::className(), ['id' => 'escort_id']);
    }

    public function getSrc()
    {
        return Yii::$app->data->getRepository('EscortPhoto')->getUserPhoto(trim($this->path), $this->host_id);
    }

    public function getLike()
    {
        return $this->getBehavior('like');
    }

    public function getLikes()
    {
        if($this->_likes === null){
            $this->_likes = $this->like->count();
        }
        return $this->_likes;
    }

    public function setLikes($value)
    {
        $this->likes = $value;
    }

    public function getIsLiked()
    {
        if($this->_isLiked === null){
            $this->_isLiked = $this->like->isImageLiked();
        }
        return $this->_isLiked;
    }

    public function setIsLiked($value)
    {
        $this->isLiked = $value;
    }

    public function getOwnerId()
    {
        return $this->escort_id;
    }

    public function getIsVerified()
    {
        return (int)$this->verified === self::VERIFIED;
    }
}
