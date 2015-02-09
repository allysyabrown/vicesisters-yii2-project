<?php

namespace app\repositories;

use app\models\Transaction;
use Yii;
use app\abstracts\Repository;
use app\models\Payment;
use app\components\PaymentServices\forms\PaymentForm;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 13.01.2015
 * Time: 10:25
 */
class PaymentRepository extends Repository
{
    public function createPayment(PaymentForm $form)
    {
        $attributes = $form->getAttributes();
        unset($attributes['id']);
        $this->entity->setAttributes($attributes);

        return $this->entity->save(false);
    }

    public function submit($id)
    {
        $payment = Payment::find()->where(['id' => $id])->one();
        if(!$payment)
            return false;

        $userId = $payment->getUserId();

        $transaction = new Transaction();
        $transaction->service_name = $payment->getServiceName();
        $transaction->sum = $payment->amount;
        $transaction->escort_id = $userId;
        $transaction->description = Yii::t('back', 'Пополнение счёта через платёжную систему {systemName}', ['systemName' => $payment->getServiceName()]);

        $result = Yii::$app->data->getRepository('Account')->addMoneyToBalance($userId, $transaction);

        if($result){
            $payment->status = Payment::STATUS_CONFIRMED;
            return $payment->update(false, ['status']);
        }

        return false;
    }

    public function reject($id)
    {
        $payment = Payment::find()->where(['id' => $id])->one();
        if(!$payment)
            return false;

        $payment->status = Payment::STATUS_REJECTED;

        return $payment->update(false, ['status']);
    }
}