<?php

namespace app\forms;

use app\models\Account;
use Yii;
use app\models\User;
use app\abstracts\BaseForm;
use app\entities\UserAccountInfo;

/**
 * LoginForm is the model behind the login form.
 *
 * @property string $userName
 * @property string $firstName
 * @property string $lastName
 * @property string $sex
 * @property array $sexItems
 */
class UserAccountForm extends BaseForm
{
    public $id;
    public $email;
    public $user_name;
    public $phone;
    public $geo;
    public $info;

    /**
     * @var \app\models\User
     */
    private $_user = false;

    /**
     * @var \app\entities\UserAccountInfo
     */
    private $_infoEntity;

    /**
     * @var array
     */
    private $_sexItems;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['email', 'user_name'], 'required'],
            [['phone'], 'integer'],
            [['phone'], 'string', 'max' => 16],
            [['geo', 'info'], 'string'],
            [['email'], 'string', 'max' => 128],
            [['user_name'], 'string', 'max' => 64]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('base', 'ID'),
            'email' => Yii::t('base', 'Email'),
            'user_name' => Yii::t('base', 'Имя пользователя'),
            'phone' => Yii::t('base', 'Телефон'),
            'geo' => Yii::t('base', 'Геолокация'),
            'info' => Yii::t('base', 'Информация'),
        ];
    }

    public function setUser(User $user)
    {
        $this->_user = $user;
        $this->setAttributes($user->getAttributes());
        $this->id = $user->id;
    }

    public function save()
    {
        if(!$this->validate())
            return false;

        return $this->model('Account')->saveUserAccount($this);
    }

    /**
     * @return \app\models\User
     */
    public function getUser()
    {
        if($this->_user === null){
            $this->_user = new User();
        }

        return $this->_user;
    }

    public function setInfoEntity($params)
    {
        $this->getInfoEntity()->setAttributes($params);
    }

    /**
     * @return \app\entities\UserAccountInfo
     */
    public function getInfoEntity()
    {
        if($this->_infoEntity === null){
            $this->_infoEntity = new UserAccountInfo();
        }

        return $this->_infoEntity;
    }

    /**
     * @return array
     */
    public function getSexItems()
    {
        if($this->_sexItems === null){
            $this->_sexItems = Account::getSexItems();
        }

        return $this->_sexItems;
    }

    public function setInfo($info)
    {
        $this->setInfoEntity($info);
    }

    public function setUserName($name)
    {
        $this->user_name = $name;
    }

    public function getUserName()
    {
        return $this->user_name;
    }

    public function setFirstName($name)
    {
        $this->getInfoEntity()->firstName = $name;
    }

    public function getFirstName()
    {
        return $this->getInfoEntity()->firstName;
    }

    public function setLastName($name)
    {
        $this->getInfoEntity()->lastName = $name;
    }

    public function getLastName()
    {
        return $this->getInfoEntity()->lastName;
    }

    public function setSex($sex)
    {
        $this->getInfoEntity()->sex = $sex;
    }

    public function getSex()
    {
        return $this->getInfoEntity()->sex;
    }
}
