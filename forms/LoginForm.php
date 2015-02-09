<?php

namespace app\forms;

use Yii;
use app\abstracts\BaseForm;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends BaseForm
{
    public $user_name;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['user_name', 'password'], 'required', 'message' => Yii::t('error', 'Это поле не может быть пустым')],
            [['user_name'], 'email', 'message' => Yii::t('error', 'Введите адрес вашей электронной почты')],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if(!$this->hasErrors()){
            $user = $this->getUser();

            if(!$user || !$user->validatePassword($this->password)){
                $this->addError($attribute, Yii::t('error', 'Неправильные имя пользователя или пароль'));
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'user_name' => Yii::t('base', 'Имя пользователя'),
            'password' => Yii::t('base', 'Пароль'),
        ];
    }

    /**
     * @param array $registration
     * @return bool
     */
    public function login($registration = null)
    {
        if($registration !== null){
            $this->setAttributes($registration);
        }

        if($this->validate()){
            return Yii::$app->user->login($this->getUser(), 3600*24*7);
        }else{
            return false;
        }
    }

    /**
     * Finds user by [[user_name]]
     *
     * @return \app\models\Account|null
     */
    public function getUser()
    {
        if($this->_user === false){
            $this->_user = Yii::$app->data->getRepository('Account')->findByUsername($this->user_name);
        }

        return $this->_user;
    }
}
