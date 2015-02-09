<?php

namespace app\models;

use app\entities\Geo;
use Yii;
use app\components\Stats;
use app\abstracts\UserEntity;

/**
 * This is the model class for table "escort".
 *
 * @property string $id
 * @property string $user_name
 * @property string $email
 * @property string $password
 * @property integer $sex
 * @property string $city_id
 * @property string $registration_date
 * @property string $last_login
 * @property string $avatar
 * @property double $balance
 * @property string $rating
 * @property string $escort_info_id
 * @property string $verification_date
 * @property boolean $is_parsed
 *
 * @property string $userName
 * @property string $gender
 * @property string $ava
 * @property boolean $isOnline
 * @property array $counters
 * @property integer $totalViews
 * @property integer $totalRating
 * @property boolean $isMe
 *
 * @property Feedback[] $feedbacks
 * @property Transaction[] $transactions
 * @property MembershipDuration[] $membershipDurations
 * @property EscortInfo $escortInfo
 * @property Membership[] $memberships
 * @property City $city
 * @property Country $country
 * @property Region $region
 * @property State $state
 * @property Message[] $messages
 * @property EscortPhoto[] $photos
 * @property string $lastLogin
 * @property string $phone
 * @property string $roleName
 * @property string $cityName
 */
class Escort extends UserEntity
{
    const ONLINE_KEY = 'online-escort:';
    const ONLINE_TIME_KEY = 'online-escort-time:';

    const TOP_PHOTOS_COUNT = 7;
    const FREE_PHOTOS_COUNT = 5;
    const PREMIUM_PHOTOS_COUNT = 20;

    public $role;
    public $isTopAnket = false;
    /**
     * @todo Разобраться, почему возникает проблема с setAttributes на этих параметрах у эскорта 71343
     */
    public $password;
    public $balance;

    /**
     * @var \app\Models\EscortGeo
     */
    private $_region;

    /**
     * @var string
     */
    private $_regionName;

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
            'rating' => [
                'value' => Yii::t('base', 'Рейтинг'),
                'sort' => true,
            ],
            'phone' => Yii::t('base', 'Телефон'),
            'gender' => Yii::t('base', 'Пол'),
            'roleName' => Yii::t('base', 'Роль'),
            'firstName' => Yii::t('base', 'Имя'),
            'lastName' => Yii::t('base', 'Фамилия'),
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
        $this->role = Account::ROLE_ESCORT;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'escort';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_name', 'email', 'password', 'sex', 'registration_date', 'last_login'], 'required'],
            [['sex', 'city_id', 'rating', 'escort_info_id'], 'integer'],
            [['balance'], 'number'],
            [['user_name'], 'string', 'max' => 64],
            [['email'], 'string', 'max' => 128],
            [['password', 'avatar'], 'string', 'max' => 512],
            [['registration_date', 'last_login', 'verification_date', 'rating', 'is_parsed'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_name' => 'User Name',
            'email' => 'Email',
            'password' => 'Password',
            'sex' => 'Sex',
            'city_id' => 'City ID',
            'registration_date' => 'Registration Date',
            'verification_date' => 'Verification Date',
            'last_login' => 'Last Login',
            'avatar' => 'Avatar',
            'balance' => 'Balance',
            'rating' => 'Rating',
            'escort_info_id' => 'Escort Info ID',
            'is_parsed' => 'Is parsed'
        ];
    }

    public function getMemberships()
    {
        return $this->hasMany(Membership::className(), ['id' => 'membership_id'])->viaTable('escort_membership', ['escort_id' => 'id']);
    }

    /**
     * @return Feedback[]
     */
    public function getFeedbacks()
    {
        return $this->hasMany(Feedback::className(), ['escort_id' => 'id']);
    }


    /**
     * @return Transaction[]
     */
    public function getTransactions()
    {
        return $this->hasMany(Transaction::className(), ['escort_id' => 'id']);
    }

    /**
     * @return MembershipDuration[]
     */
    public function getMembershipDurations()
    {
        return $this->hasMany(MembershipDuration::className(), ['escort_id' => 'id']);
    }

    /**
     * @return EscortInfo
     */
    public function getEscortInfo()
    {
        return $this->hasOne(EscortInfo::className(), ['escort_id' => 'id']);
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    /**
     * @return static
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id'])->viaTable(City::tableName(), ['id' => 'city_id']);
    }

    /**
     * @return static
     */
    public function getState()
    {
        return $this->hasOne(State::className(), ['code' => 'state_code'])->viaTable(City::tableName(),['id' => 'city_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Message::className(), ['sender' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhotos()
    {
        return $this->hasMany(EscortPhoto::className(), ['escort_id' => 'id']);
    }

    public function beforeSave($insert)
    {
        if($insert !== false && $this->getIsNewRecord()){
            $emailParts = explode('@', $this->email);
            $this->user_name = $emailParts[0];

            if($this->sex == null){
                $this->sex = Account::SEX_FEMALE;
            }
            if(!$this->registration_date){
                $this->registration_date = Yii::$app->local->dateTime();
            }
            if(!$this->user_name){
                if(!$this->email){
                    $this->addError(Yii::t('error', 'Необходимо указать адрес электронной почты'));
                    return false;
                }

                $emailParts = explode('@', $this->email);
                $this->user_name = $emailParts[0];
            }

            if(!$this->registration_date){
                $this->registration_date = Yii::$app->local->dateTime();
            }
        }

        return parent::beforeSave($insert);
    }

    public function getGender()
    {
        switch($this->sex){
            case Account::SEX_MALE:
                return Yii::t('account', 'Мужчина');
            case Account::SEX_FEMALE:
                return Yii::t('account', 'Женщина');
            default:
                return Yii::t('account', 'Транссексуал');
        }
    }

    public function setFirstName($name)
    {
        $this->escortInfo->first_name = $name;
    }

    public function getFirstName()
    {
        return $this->escortInfo ? $this->escortInfo->getFirstName() : '';
    }

    public function setLastName($name)
    {
        $this->escortInfo->last_name = $name;
    }

    public function getLastName()
    {
        return $this->escortInfo ? $this->escortInfo->getLastName() : '';
    }

    public function getRegistrationDate()
    {
        return $this->registration_date;
    }

    public function getLastLogin()
    {
        return $this->last_login;
    }

    public function setPhone($phone)
    {
        $this->escortInfo->phone = $phone;
    }

    public function getPhone()
    {
        return $this->escortInfo ? $this->escortInfo->phone : '';
    }

    public function getRoleName()
    {
        return Account::roleName($this->role);
    }

    public function getVerificationDate()
    {
        return $this->verification_date;
    }

    public function getIsVerified()
    {
        return $this->verification_date != null;
    }

    public function getCityName()
    {
        return $this->city ? $this->city->name : '';
    }

    protected function getOnlineKey()
    {
        return self::ONLINE_KEY.$this->id;
    }

    protected function getOnlineTimeKey()
    {
        return self::ONLINE_TIME_KEY.$this->id;
    }

    public function getIsVip()
    {
        return $this->getMembershipDurations()->where(['membership_id' => Membership::VIP_ACCOUNT])->one();
    }

    public function getIsPremium()
    {
        return $this->getMembershipDurations()->where(['membership_id' => Membership::PREMIUM_ACCOUNT])->one();
    }

    public function canUploadPhotos()
    {
        if($this->getIsVip())
            return true;

        if($this->getIsPremium()){
            if(intval(EscortPhoto::find()->where(['escort_id' => $this->id])->count('id')) < self::PREMIUM_PHOTOS_COUNT) {
                return true;
            }
            return false;
        }

        if(intval(EscortPhoto::find()->where(['escort_id' => $this->id])->count('id')) < self::FREE_PHOTOS_COUNT) {
            return true;
        }

        return false;
    }

    public function checkOnlineTime()
    {
        $key = $this->getOnlineTimeKey();
        $fastData = Yii::$app->fastData;
        $lastCheck = $fastData->get($key);

        $now = time();

        if(!$lastCheck){

            $fastData->set($key, $now);
            return $fastData->expire($key, Yii::$app->params['onlineTimeCheck']);

        } elseif($this->diffMoreThan($lastCheck,$now)){

            Yii::$app->stats->escortRating($this->id)->add(Yii::$app->params['onlineRatingIncrementor']);
            Yii::$app->stats->escortOnline($this->id)->add();

            $fastData->set($key, $now);
            return $fastData->expire($key, Yii::$app->params['onlineTimeCheck']);

        }

        return false;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function getIsOnline($id = null)
    {
        if($id === null)
            $id = $this->id;
        return (bool)Yii::$app->fastData->get(Escort::ONLINE_KEY.$id);
    }

    /**
     * @return array | null
     */
    public function getCounters()
    {
        return Yii::$app->fastData->hvals(Stats::KEY_ESCORT_STATS.$this->id);
    }

    /**
     * @return int
     */
    public function getTotalViews()
    {
        return (integer)Yii::$app->fastData->hget(Stats::KEY_ESCORT_STATS.$this->id, Stats::FIELD_VIEWS);
    }

    /**
     * @return int
     */
    public function getTodayViews()
    {
        return (integer)Yii::$app->stats->escortViews($this->id)->today();
    }

    /**
     * @return int
     */
    public function getTotalRating()
    {
        return (integer)Yii::$app->fastData->hget(Stats::KEY_ESCORT_STATS.$this->id, Stats::FIELD_RATING);
    }

    public function getRegionName()
    {
        if($this->_regionName === null){
            $geo = $this->getRegion();

            $this->_regionName = '';

            if($geo->city)
                $this->_regionName .= $geo->city->name;
            if($geo->country)
                $this->_regionName .= $this->_regionName !== '' ? ', '.$geo->country->name : $geo->country->name;
        }

        return $this->_regionName;
    }

    /**
     * @return EscortGeo
     */
    public function getRegion()
    {
        if($this->_region === null){
            $this->_region = $this->model('EscortGeo')->findRegionsFoolInfo($this->id);
        }

        return $this->_region;
    }

    /**
     * @return bool
     */
    public function getIsMe()
    {
        return $this->id == Yii::$app->user->id;
    }

    public function canAddFeed()
    {
        return !Yii::$app->user->isGuest;
    }

    public function canAddFeedback()
    {
        return !Yii::$app->data->getRepository('Feedback')->getMineFeedback($this->id)
                && !$this->getIsMe()
                && Yii::$app->user->getRole() == Account::ROLE_USER;
    }

    public function haveTopPhotos()
    {
        return count($this->photos) >= static::TOP_PHOTOS_COUNT;
    }
}
