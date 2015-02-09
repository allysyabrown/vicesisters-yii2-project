<?php

namespace app\forms;

use app\abstracts\UserEntity;
use Yii;
use app\abstracts\BaseForm;

/**
 * Created by PhpStorm.
 * User: jangolle
 * Date: 12.12.2014
 * Time: 11:10
 */
class ChangePasswordForm extends BaseForm
{
    public $oldPassword;
    public $newPassword;
    public $retypePassword;

    private $_user;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['retypePassword'], 'compare', 'compareAttribute' => 'newPassword', 'message' => \Yii::t('error', 'Пароли не совпадают')],
            [['newPassword', 'retypePassword', 'oldPassword'], 'string', 'message' => \Yii::t('error', 'Это текстовое поле')],
        ];
    }

    public function attributeLabels()
    {
        return [
            'oldPassword' => Yii::t('account', 'Старый пароль'),
            'newPassword' => Yii::t('account', 'Новый пароль'),
            'retypePassword' => Yii::t('account', 'Повторите новый пароль'),
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

            if(!$user || !$user->validatePassword($this->newPassword)){
                $this->addError($attribute, \Yii::t('error', 'Неправильные имя пользователя или пароль'));
            }
        }
    }

    public function save()
    {
        if(!$this->validate())
            return false;

        $newPassword = UserEntity::passwordHash($this->newPassword);
        if(UserEntity::passwordHash($this->oldPassword) !== Yii::$app->user->getIdentity()->password){
            $this->addError('password', 'Неверный старый пароль');
            return false;
        }

        return Yii::$app->data->getRepository('Account')->setPassword(Yii::$app->user->id, $newPassword);
    }
} 