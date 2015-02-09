<?php

namespace app\components\PaymentServices\forms;

use Yii;
use app\abstracts\BaseForm;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 13.11.14
 * Time: 14:01
 */
class PaymentForm extends BaseForm
{
    public $id;
    public $userId;
    public $transferId;
    public $amount;
    public $userName;
    public $city;
    public $country;
    public $serviceName;
} 