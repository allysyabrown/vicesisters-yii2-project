<?php

namespace app\components\PaymentServices\forms;

use Yii;
use app\components\Payment as PaymentComponent;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 12.01.2015
 * Time: 18:04
 */
class WuForm extends PaymentForm
{
    public function rules()
    {
        return [
            [['transferId', 'amount', 'userName', 'city', 'country'], 'required', 'message' => Yii::t('error', 'Это поле не может быть пустым')],
            [['amount'], 'double', 'message' => Yii::t('payment', 'Цена может быть только числом')],
            [['serviceName'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('base', 'ID'),
            'userId' => Yii::t('payment', 'ID Пользователя'),
            'transferId' => Yii::t('payment', 'ID платежа WU'),
            'amount' => Yii::t('payment', 'Сумма'),
            'userName' => Yii::t('payment', 'Ваше имя'),
            'city' => Yii::t('payment', 'Город'),
            'country' => Yii::t('payment', 'Страна'),
        ];
    }

    public function save()
    {
        if($this->userId === null)
            $this->userId = Yii::$app->user->id;
        if($this->userName === null)
            $this->userName = Yii::$app->user->getEntity()->getFullName();
        if($this->serviceName === null)
            $this->serviceName = PaymentComponent::WU_ID;

        if(!$this->validate())
            return false;

        return $this->model('Payment')->createPayment($this);
    }
}