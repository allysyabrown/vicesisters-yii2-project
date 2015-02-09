<?php

namespace app\abstracts;

use app\components\FastData;
use app\components\StaticData;
use app\models\Escort;
use app\models\User;
use Yii;
use app\models\Account;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 11.11.14
 * Time: 12:50
 *
 * @property string $password
 * @property string $role
 * @property integer $id
 * @property string $last_login
 * @property string $lastLogin
 * @property string $user_name
 * @property string $email
 * @property string $url
 * @property string $userName
 * @property string $ava
 * @property string $avatar
 *
 * @property string $fullName
 * @property string $loginUrl
 * @property string $region
 * @property array $myPhotos
 * @property array $photosForPremium
 *
 * @method getFirstName
 * @method getLastName
 */
abstract class UserEntity extends BaseModel
{
    private static $_salt = 'iuIjdf98SDFDSfksjdf87';

    private $_defaultAvatar = 'http://chat.vicesisters.com/web/img/vs-default-user-avatar.png';

    /**
     * @var array
     */
    private $_photos;

    /**
     * @var array
     */
    private $_premiumPhotos;

    private $_fullName;

    private $_randomImg;

    public function setActivity(){
        if(trim($this->role) == Account::ROLE_ADMIN)
            return true;

        $key = $this->getOnlineKey();
        $fastData = Yii::$app->fastData;
        $user = $fastData->get($key);

        if(!$user)
            $fastData->set($key, $this->id);

        return $fastData->expire($key, Yii::$app->params['userOnlineTime']);
    }

    public function setNonActive(){
        $fastData = Yii::$app->fastData;
        return $fastData->del($this->getOnlineKey());
    }

    protected function getOnlineKey()
    {
        $userClass = Yii::$app->user->getIdentity()->getUserClass();

        return $userClass::ONLINE_KEY.$this->id;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return static::passwordHash($password) == trim($this->password);
    }

    public static function passwordHash($password)
    {
        return crypt($password, '$6$rounds=5120$'.self::$_salt.'$');
    }

    public function getRole()
    {
        return trim($this->role);
    }

    public static function getSalt()
    {
        return self::$_salt;
    }

    public function setLastLogin($time)
    {
        $this->last_login = $time;
    }

    public function getLastLogin()
    {
        return $this->last_login;
    }

    public function afterLogin()
    {
        if(Yii::$app->user->getIsEscort() || Yii::$app->user->getIsUser())
            $this->setLastLoginTime();

        $url = '#';
        if(Yii::$app->user->getIsUser())
            $url = Url::toRoute(['user/profile', 'id' => Yii::$app->user->id], true);
        elseif(Yii::$app->user->getIsEscort())
            $url = Url::toRoute(['escort/profile', 'id' => Yii::$app->user->id], true);

        Yii::$app->chat->addUser(Yii::$app->user->id, [
            'name' => Yii::$app->user->getFullName(),
            'avatar' => Yii::$app->user->getAva(),
            'url' => $url,
        ]);
    }

    public function getHomeUrl()
    {
        if($this->getRole() === Account::ROLE_GUEST)
            return Url::to(['index/index']);

        return $this->getRole() === Account::ROLE_USER ? Url::to(['user/profile','id' => $this->id]) : Url::to(['escort/profile','id' => $this->id]);
    }

    public function getDefaultAvatar()
    {
        return $this->_defaultAvatar;
    }

    public function setUserName($name)
    {
        $this->user_name = $name;
    }

    public function getUserEmail()
    {
        if($this->hasAttribute('email'))
            return $this->getAttribute('email');
        elseif($this->hasAttribute('user_name'))
            return $this->getAttribute('user_name');
        else
            return $this->getUserName();
    }

    public function getUserName()
    {
        $userName = trim($this->user_name);
        if(!$userName && property_exists('email', $this) && $this->email){
            $userName = trim($this->email);
            $userName = explode('@', $userName);
            $userName = array_shift($userName);
        }
        return $userName;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        if($this->_fullName === null){
            $this->_fullName = $this->getFirstName() ? $this->getFirstName().' '.$this->getLastName() : $this->getUserName();
        }
        return $this->_fullName;
    }

    public function getAva()
    {
        return $this->avatar ? Yii::$app->data->getRepository('EscortPhoto')->getUserPhoto(trim($this->avatar)) : $this->getDefaultAvatar();
    }

    public function getRandomImg()
    {
        if($this->_randomImg === null){
            $this->_randomImg = $this->model('EscortPhoto')->getRandomEscortPhoto($this->id);
        }
        return $this->_randomImg;
    }

    public function getAvatar()
    {
        return $this->getAva();
    }

    protected function setLastLoginTime()
    {
        $this->setLastLogin(Yii::$app->local->dateTime());
        return Yii::$app->getDb()->createCommand()->update($this->tableName(), ['last_login' => Yii::$app->local->dateTime()], ['id' => $this->id])->execute();
    }

    public function checkOnlineTime()
    {

    }

    public function getUrl()
    {
        return Url::toRoute(['escort/profile', 'id' => $this->id]);
    }

    public function getLoginUrl($name = null)
    {
        if($name === null)
            $name = $this->getUserEmail();

        $params = [
            'class' => 'ask-before',
            'data' => [
                'ask' => Yii::t('back', 'Вы действительно хотите залогиниться под этим пользователем?'),
            ],
        ];

        return Html::a($name, Url::toRoute(['account/loginbyuserid', 'id' => $this->id]), $params);
    }

    /**
     * @return array
     */
    public function getPhotosForPremium()
    {
        if($this->_premiumPhotos === null){
            $this->_premiumPhotos = [];
            $myPhotos = $this->getMyPhotos();

            if($myPhotos){
                $count = count($myPhotos);
                for($i = 0; $i < 5; $i++){
                    $this->_premiumPhotos[] = $myPhotos[rand(0, $count-1)];
                }
            }
        }

        return $this->_premiumPhotos;
    }

    /**
     * @return array
     */
    public function getMyPhotos()
    {
        if($this->role != Account::ROLE_ESCORT)
            return false;

        if($this->_photos === null){
           $this->_photos = Yii::$app->data->getRepository('EscortPhoto')->findByEscortId($this->id);
        }

        return $this->_photos;
    }

    public function getRegion()
    {
        return '';
    }

    public function getIsAdmin()
    {
        return $this->getRole() === Account::ROLE_ADMIN;
    }

    public function getIsEscort()
    {
        return $this->getRole() === Account::ROLE_ESCORT || $this->getRole() === Account::ROLE_VERIFIED_ESCORT;
    }

    public function getIsUser()
    {
        return $this->getRole() === Account::ROLE_USER;
    }

    /**
     * @param $sTime
     * @param $eTime
     * @param int $diff
     * @return bool
     */
    protected function diffMoreThan($sTime,$eTime,$diff = 3600)
    {
        return ($eTime - $sTime) >= $diff;
    }
}