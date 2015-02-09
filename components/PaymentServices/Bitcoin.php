<?php

namespace app\components\PaymentServices;

use Yii;
use app\components\Payment;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 12.11.14
 * Time: 14:35
 */
class Bitcoin extends Service
{
    public $name = 'Bitcoin';
    public $remoteServiceUrl;
    public $secret;
    public $bitcoinAddress;
    public $url = 'payment/bitcoin';
    public $confirmUrl = 'payment/bitcoinconfirm';
    public $infoUrl = 'payment/bitcoinmoney';
    public $baseUrl;
    public $logo = '/frontend/img/settings_credits_bit_logo.png';
    public $sumDiff = 0.0001;
    public $confirmations = 4;

    private $_btc;
    private $_requestParams;
    private $_oldParams;
    private $_sumInBTC;
    private $_userId;

    public function getBitcoins()
    {
        if($this->_btc === null){
            if(!$this->getAmount())
                return false;

            $btc = file_get_contents($this->remoteServiceUrl.'tobtc?currency='.$this->systemCurrency.'&value='.$this->getAmount());
            $btc = floatval($btc);

            if($btc <= 0){
                $this->addError(Yii::t('payment', 'Сумма должна быть больше нуля!'));
                return false;
            }

            $this->_btc = $btc;
        }

        return $this->_btc;
    }

    public function getQrImage()
    {
        return $this->remoteServiceUrl.'qr?data=bitcoin:'.$this->bitcoinAddress.'%3Famount='.$this->getBitcoins().'%26label=Pay-Demo&size=125';
    }

    public function getBitcoinScriptSrc()
    {
        return $this->remoteServiceUrl.'Resources/wallet/pay-now-button-v2.js';
    }

    public function getCallBackUrl()
    {
        return $this->getBaseUrl().Url::toRoute([$this->confirmUrl, 'user' => Yii::$app->user->id, 'secret' => $this->secret]);
    }

    public function getInputAddress()
    {
        $response = Json::decode(file_get_contents($this->remoteServiceUrl.'api/receive?method=create&callback='.$this->getCallBackUrl().'&address='.$this->bitcoinAddress));
        if(!$response || !isset($response['input_address'])){
            $this->addError('Не удалось получить ответ от удалённого сервиса');
            return false;
        }

        return $response['input_address'];
    }

    public function setPaymentInfo($userId, $info = [])
    {
        if(empty($info)){
            $info = [
                'user' => Yii::$app->user->id,
                'secret' => $this->secret,
                'currency' => $this->systemCurrency,
                'bitcoins' => $this->getBitcoins(),
                'credits' => $this->getAmount(),
            ];
        }

        parent::setPaymentInfo($userId, $info);
    }

    public function confirm($params = [])
    {
        $this->_requestParams = $params;
        $paramsError = 'Неверные параметры запроса';

        $userId = isset($params['user']) ? intval($params['user']) : null;
        if($userId === null || $userId === 0)
            return $this->confirmError($paramsError);
        $this->_userId = $userId;

        $sum = isset($params['value']) ? intval($params['value']) : null;
        if($sum === null || $sum === 0)
            return $this->confirmError($paramsError);
        $sum = $sum/100000000;
        $this->_sumInBTC = $sum;

        $secret = isset($params['secret']) ? $params['secret'] : null;
        if($secret === null || $secret === '')
            return $this->confirmError($paramsError);

        $confirmations = isset($params['confirmations']) ? intval($params['confirmations']) : null;
        if($confirmations === null)
            return $this->confirmError($paramsError);

        $oldParams = $this->getPaymentInfo($userId);
        if(is_null($oldParams) || empty($oldParams))
            return $this->confirmError($paramsError);
        $this->_oldParams = $oldParams;

        $diff = abs($sum - $oldParams['bitcoins']);
        if($diff > $this->sumDiff)
            return $this->confirmError('Неверная сумма');

        if($secret !== $oldParams['secret'] || $secret !== $this->secret)
            return $this->confirmError($paramsError);

        if($confirmations < $this->confirmations)
            return $this->confirmError('Недостаточно подтверждений', true);

        Yii::$app->user->addToBalance($oldParams['credits'], $this->id, $userId);

        Yii::$app->fastData->del(Payment::BITCOIN_CONFIRM_KEY.':'.$userId);
        Yii::$app->fastData->del(Payment::BITCOIN_REDIS_KEY.':'.$userId);

        return '*ok*';
    }

    private function confirmError($error, $confirmations = false)
    {
        $error = Yii::t('error', $error);

        Yii::$app->fastData->set(Payment::BITCOIN_CONFIRM_KEY.':'.$this->_userId, json_encode([
            'error' => $error,
            'user' => $this->_userId,
            'sumInBTC' => $this->_sumInBTC,
            'requestParams' => $this->_requestParams,
            'oldParams' => $this->_oldParams
        ]));

        return $confirmations ? 'Waiting for confirmations' : $error;
    }
} 