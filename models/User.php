<?php

namespace app\models;

use Yii;
use app\entities\UserAccountInfo;
use app\abstracts\UserEntity;

/**
 * This is the model class for table "user".
 *
 * @property string $id
 * @property string $email
 * @property string $user_name
 * @property string $phone
 * @property string $geo
 * @property string $info
 * @property string $avatar
 * @property string $registration_date
 * @property string $last_login
 *
 * @property boolean $isMe
 * @property boolean $isOnline
 *
 * @property Feedback[] $feedbacks
 * @property PrivateMessage[] $privateMessages
 *
 * @property string $firstName
 * @property string $lastName
 * @property string $registrationDate
 * @property string $lastLogin
 */
class User extends UserEntity
{
    const ONLINE_KEY = 'online-user:';

    private $_defaultAvatar = '/frontend/img/vs-default-user-avatar.png';

    private $_info;

    public $role;

    public static function tableAttributes()
    {
        return [
            'id' => [
                'value' => Yii::t('base', 'ID'),
                'type' => 'int',
                'sort' => true,
                'search' => true,
            ],
            'loginUrl' => [
                'value' => Yii::t('base', 'Email'),
                'sort' => 'email',
                'search' => [
                    'name' => 'email',
                    'cond' => 'LIKE',
                ],
            ],
            'firstName' => [
                'name' => 'user_name',
                'value' => Yii::t('base', 'Имя'),
                'sort' => true,
                'search' => 'LIKE',
            ],
            'lastName' => Yii::t('base', 'Фамилия'),
            'phone' => Yii::t('base', 'Телефон'),
            'registrationDate' => [
                'name' => 'registration_date',
                'value' => Yii::t('base', 'Регистрация'),
                'sort' => true,
            ],
            'lastLogin' => [
                'name' => 'last_login',
                'value' => Yii::t('base', 'Последнее посещение'),
                'sort' => true,
            ],
        ];
    }

    public function init()
    {
        $this->role = Account::ROLE_USER;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'user_name'], 'required'],
            [['geo', 'info', 'avatar'], 'string'],
            [['email'], 'string', 'max' => 128],
            [['user_name', 'phone'], 'string', 'max' => 64]
        ];
    }

    public function beforeSave($insert = false)
    {
        if($this->getIsNewRecord()){
            if($this->getRegistrationDate() === null)
                $this->setRegistrationDate(Yii::$app->local->dateTime());
        }

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'user_name' => 'User Name',
            'phone' => 'Phone',
            'geo' => 'Geo',
            'info' => 'Info',
            'avatar' => 'Avatar',
        ];
    }

    public function setFeedbacks($attributes)
    {
        $records = $this->setObjectsAttributes(Feedback::className(), $attributes);
        if($records !== null)
            $this->populateRelation('feedbacks', $records);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedbacks()
    {
        return $this->hasMany(Feedback::className(), ['user_id' => 'id']);
    }


    // Getters & setters

    public function setFirstName($name)
    {
        $this->getInfoEntity()->firstName = $name;
    }

    public function getFirstName()
    {
        return $this->getInfoEntity()->firstName ? $this->getInfoEntity()->firstName : null;
    }

    public function setLastName($name)
    {
        $this->getInfoEntity()->lastName = $name;
    }

    public function getLastName()
    {
        return $this->getInfoEntity()->lastName ? $this->getInfoEntity()->lastName : null;
    }

    /**
     * @return UserAccountInfo
     */
    private function getInfoEntity()
    {
        if($this->_info === null){
            $this->_info = new UserAccountInfo();
            $this->_info->setAttributes(json_decode($this->info),true);
        }

        return $this->_info;
    }

    /**
     * @return bool
     */
    public function getIsMe()
    {
        return $this->id == Yii::$app->user->id;
    }

    /**
     * @return bool
     */
    public function getIsOnline()
    {
        return (bool)Yii::$app->fastData->get(User::ONLINE_KEY.$this->id);
    }

    public function setRegistrationDate($date)
    {
        $this->registration_date = $date;
    }

    public function getRegistrationDate()
    {
        return $this->registration_date;
    }

    public function setLastLogin($date)
    {
        $this->last_login = $date;
    }

    public function getLastLogin()
    {
        return $this->last_login;
    }

    // END Getters & setters
}
