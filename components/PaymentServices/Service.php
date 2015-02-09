<?php

namespace app\components\PaymentServices;

use Yii;
use app\abstracts\BaseComponent;
use app\components\Payment;
use yii\base\ErrorException;
use app\components\PaymentServices\forms\PaymentForm;
use yii\helpers\Url;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 12.11.14
 * Time: 14:43
 *
 * @property string $buttonHandler
 * @property string $logoImage
 * @property string $view
 * @property string $formView
 * @property string $infoView
 * @property string $balanceView
 */
class Service extends BaseComponent
{
    public $id;
    public $name;
    public $url;
    public $viewPath = '@app/modules/frontend/views/payment/';
    public $viewExt = '.twig';
    public $systemCurrency;
    public $baseUrl;
    public $infoUrl;
    public $secret;
    public $logo;

    /**
     * @var \app\components\PaymentServices\forms\PaymentForm
     */
    protected $_form;

    protected $_amount = 0;

    public function setParams(array $params)
    {
        foreach($params as $name => $value){
            $setter = 'set'.ucfirst($name);

            if(method_exists($this, $setter)){
                $this->$setter($value);
            }elseif(property_exists($this, $name)){
                $this->$name = $value;
            }
        }
    }

    public function getView()
    {
        return $this->viewPath.'_'.$this->id.$this->viewExt;
    }

    public function getFormView()
    {
        return $this->viewPath.$this->id.'Form'.$this->viewExt;
    }

    public function getInfoView()
    {
        return $this->viewPath.$this->id.'Info'.$this->viewExt;
    }

    public function getBalanceView()
    {
        return $this->viewPath.'_balance'.$this->viewExt;
    }

    public function getInfoViewName()
    {
        return $this->id.'Info';
    }

    /**
     * @param PaymentForm $form
     * @return $this
     */
    public function setFrom(PaymentForm $form)
    {
        $this->_form = $form;

        $this->_amount = floatval($form->amount);

        if(!$this->_amount > 0){
            $this->addError(Yii::t('payment', 'Сумма должна быть больше нуля!'));
        }

        return $this;
    }

    /**
     * @return \app\components\PaymentServices\forms\PaymentForm
     * @throws \yii\base\ErrorException
     */
    public function getForm()
    {
        switch($this->id){
            case Payment::BITCOIN_ID:
                return new \app\components\PaymentServices\forms\BitcoinForm();
            case Payment::WU_ID:
                $form = new \app\components\PaymentServices\forms\WuForm();
                $form->serviceName = $this->id;
                return $form;
        }

        throw new ErrorException(Yii::t('payment', 'Не удалось найти форму для  сервиса {service}!', ['service' => $this->name]));
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->_amount;
    }

    public function getCallBackUrl()
    {
        return '';
    }

    public function getButtonHandler()
    {
        switch($this->id){
            case Payment::BITCOIN_ID:
                return 'Bitcoin.setButtons("'.Url::toRoute([$this->infoUrl]).'", "'.$this->getBitcoinScriptSrc().'")';
            default:
                return '';
        }
    }

    public function setPaymentInfo($userId, $info = [])
    {
        switch($this->id){
            case Payment::BITCOIN_ID:
                Yii::$app->fastData->set(Payment::BITCOIN_REDIS_KEY.':'.$userId, json_encode($info));
                break;
            default:
                break;
        }
    }

    public function getPaymentInfo($userId)
    {
        $info = Yii::$app->fastData->get(Payment::BITCOIN_REDIS_KEY.':'.$userId);
        if($info)
            $info = json_decode($info, true);

        return $info;
    }

    public function getLogoImage()
    {
        return $this->logo;
    }

    public function confirm($params = [])
    {
        return false;
    }

    protected function getBaseUrl()
    {
        if(!$this->baseUrl)
            $this->baseUrl = Yii::$app->request->hostInfo;

        return $this->baseUrl;
    }
}