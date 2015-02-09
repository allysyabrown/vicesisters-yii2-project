<?php

namespace app\components\PaymentServices\forms;

use Yii;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 13.11.14
 * Time: 13:59
 */
class BitcoinForm extends PaymentForm
{
    public function rules()
    {
        return [
            [['amount'], 'required', 'message' => \Yii::t('error', 'Это поле не может быть пустым')],
            [['amount'], 'double', 'message' => \Yii::t('payment', 'Цена может быть только числом')],
        ];
    }

    public function attributeLabels()
    {
        return [
            'amount' => '',
        ];
    }
} 