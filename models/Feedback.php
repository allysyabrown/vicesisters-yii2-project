<?php

namespace app\models;

use Yii;
use app\abstracts\BaseModel;
use app\models\User;

/**
 * This is the model class for table "feedback".
 *
 * @property string $id
 * @property string $escort_id
 * @property string $user_id
 * @property string $text
 * @property string $extensions
 * @property string $time
 * @property string $avatar
 *
 * @property User $user
 * @property Escort $escort
 */
class Feedback extends BaseModel
{

    public $avatar;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'feedback';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['escort_id', 'user_id', 'time'], 'required'],
            [['escort_id', 'user_id'], 'integer'],
            [['text', 'extensions'], 'string'],
            [['time'], 'safe']
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
            'user_id' => 'User ID',
            'text' => 'Text',
            'extensions' => 'Extensions',
            'time' => 'Time',
        ];
    }

    public function setUser($attributes)
    {
        $escortInfo = $this->setObjectAttributes(new User(), $attributes);
        if($escortInfo !== null)
            $this->populateRelation('user', $escortInfo);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function setEscort($attributes)
    {
        $escortInfo = $this->setObjectAttributes(new Escort(), $attributes);
        if($escortInfo !== null)
            $this->populateRelation('escort', $escortInfo);
    }

    public function getAvatar()
    {
        $account = Yii::$app->data->getRepository('Account')->findEntityById($this->user_id);
        return $account ? $account->ava : (new User())->getDefaultAvatar();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEscort()
    {
        return $this->hasOne(Escort::className(), ['id' => 'escort_id']);
    }

    public function beforeSave($insert)
    {
        if($this->getIsNewRecord()){
            if($this->user_id === null)
                $this->user_id = Yii::$app->user->id;
            if($this->time === null)
                $this->time = Yii::$app->local->dateTime();
        }

        return parent::beforeSave($insert);
    }

    public function getOwnerId()
    {
        return $this->user_id;
    }
}
