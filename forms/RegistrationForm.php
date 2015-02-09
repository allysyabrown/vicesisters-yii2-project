<?php

namespace app\forms;

use Yii;
use app\models\Account;
use app\abstracts\BaseForm;

/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 27.10.2014
 * Time: 20:49
 */
class RegistrationForm extends BaseForm
{
    public $user_name;
    public $phone;
    public $description;
    public $password;
    public $retypePassword;
    public $role;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['user_name', 'phone', 'role', 'password', 'retypePassword'], 'required', 'message' => \Yii::t('error', 'Это поле не может быть пустым')],
            [['user_name'], 'unique', 'message' => \Yii::t('error', 'Такой пользователь уже зарегистрирован в системе')],
            [['user_name'], 'email', 'message' => \Yii::t('error', 'Введите адрес вашей электронной почты')],
            [['retypePassword'], 'compare', 'compareAttribute' => 'password', 'message' => \Yii::t('error', 'Пароли не совпадают')],
            [['user_name', 'password', 'retypePassword', 'user_name'], 'string', 'message' => \Yii::t('error', 'Это текстовое поле')],
            [['user_name', 'phone', 'password', 'retypePassword'], 'string', 'max' => 512, 'message' => \Yii::t('error', 'Длина этого поля не может превышать {count} символов', ['count' => 512])],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_name' => Yii::t('base', 'E-mail'),
            'phone' => Yii::t('base', 'Телефон'),
            'role' => Yii::t('front', 'Зарегистрироваться как'),
            'password' => Yii::t('base', 'Пароль'),
            'retypePassword' => Yii::t('base', 'Повторите пароль'),
        ];
    }

    public function save()
    {
        if(!$this->validate())
            return false;

        return Yii::$app->data->getRepository('Account')->addUser($this);
    }

    public static function find()
    {
        return Account::find();
    }

    public function getRoleItems()
    {
        return [
            Account::ROLE_USER => Yii::t('base', 'Пользователь'),
            Account::ROLE_ESCORT => Yii::t('base', 'Эскорт'),
        ];
    }
}