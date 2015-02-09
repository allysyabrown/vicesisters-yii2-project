<?php

namespace app\components;

use Yii;
use app\abstracts\BaseComponent;
use yii\base\ErrorException;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 12.11.14
 * Time: 14:05
 *
 * @property $currency
 */
class Payment extends BaseComponent
{
    const BITCOIN_ID = 'bitcoin';
    const WU_ID = 'wu';

    const BITCOIN_REDIS_KEY = 'bitcoin_payment_info';
    const BITCOIN_CONFIRM_KEY = 'bitcoin_payment_confirm';

    private static $_currencies = [
        'USD' => 'Кредитов'
    ];

    /**
     * @var \app\components\PaymentServices\forms\PaymentForm[]
     */
    private $_forms;

    /**
     * @var \app\components\PaymentServices\Service[]
     */
    public $services;
    public $servicesNamespace = 'app\components\PaymentServices';
    public $baseService = 'app\components\PaymentServices\Service';
    public $systemCurrency;

    public function init()
    {
        $services = $this->services;
        $this->services = [];

        if($services){
            foreach($services as $name => $params){
                $this->addService($name, (array)$params);
            }
        }
    }

    /**
     * @return \app\components\PaymentServices\Service[]
     */
    public function getServices()
    {
        return $this->services;
    }

    public function addService($service, array $params)
    {
        $className = $this->servicesNamespace.'\\'.ucfirst($service);

        if(!class_exists($className))
            throw new ErrorException(Yii::t('payment', 'Сервис {service} не найден!', ['service' => $className]));

        $newService = new $className();

        $instance = $this->baseService;

        if(!$newService instanceof $instance){
            throw new ErrorException(Yii::t('payment', 'Сервис {service} должен наследовать класс {instance}', ['service' => $className, 'instance' => $instance]));
        }

        $newService->systemCurrency = $this->systemCurrency;
        $newService->setParams($params);
        $name = lcfirst($service);
        $newService->id = $name;
        $this->services[$name] = $newService;
    }

    public function getService($name)
    {
        if($name === null)
            $name = self::BITCOIN_ID;

        if(!isset($this->services[$name]))
            throw new ErrorException(Yii::t('payment', 'Сервис {service} не найден!', ['service' => $name]));

        return $this->services[$name];
    }

    public function getCurrency()
    {
        $currency = isset(self::$_currencies[$this->systemCurrency]) ? self::$_currencies[$this->systemCurrency] : $this->systemCurrency;
        return Yii::t('base', $currency);
    }

    /**
     * @return \app\components\PaymentServices\forms\PaymentForm[]
     * @throws ErrorException
     */
    public function getForms()
    {
        if($this->_forms === null){
            $this->_forms = [];

            foreach($this->getServices() as $service){
                $this->_forms[$service->id.'Form'] = $service->getForm();
            }
        }

        return $this->_forms;
    }
}