<?php

namespace app\modules\frontend\controllers;

use Yii;
use app\abstracts\FrontController;
use app\components\Payment;
use app\forms\ChangePasswordForm;
use app\forms\EscortAccountForm;
use app\forms\HotMessageForm;
use yii\web\NotFoundHttpException;
use app\forms\EscortTravelsForm;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 12.11.14
 * Time: 14:07
 */
class PaymentController extends FrontController
{
    private $_service;

    public function beforeAction($action)
    {
        if(stripos($this->action->id, 'bitcoin') !== false){
            $this->setService(Payment::BITCOIN_ID);
        }

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
       return $this->render();
    }

    // Bitcoin actions

    public function actionBitcoin()
    {
        $service = $this->getService();
        $form = $service->getForm();

        if($this->isAjax()){
            $this->ajax('payment/_bitcoin', [
                'formView' => $service->getInfoView(),
                'bitcoinForm' => $form,
                'bitcoin' => $service,
            ]);
        }

        return $this->render([
            'pageName' => Yii::t('payment', 'Bitcoin платежи'),
            'bitcoin' => $service,
            'formView' => $service->getFormView(),
            'bitcoinForm' => $form,
        ]);
    }

    public function actionBitcoinmoney()
    {
        $this->validateAjax();

        $service = $this->getService();
        $form = $service->getForm();

        if($form->load($this->getPost()) && $form->validate()){
            $service->setFrom($form);
            if(!$service->hasError()){
                $service->setPaymentInfo(Yii::$app->user->id);

                $this->ajax($service->getInfoViewName(), [
                    'bitcoin' => $service,
                    'serviceScript' => $service->getBitcoinScriptSrc(),
                ]);
            }else{
                $this->ajaxError($service->getError());
            }
        }
    }

    public function actionBitcoininfo()
    {
        $this->validateAjax();

        $address = $this->getService()->getInputAddress();

        if(!$address)
            $this->ajaxError($this->getService()->getError());

        $this->ajax(['input_address' => $address]);
    }

    public function actionBitcoinconfirm($user, $secret)
    {
        echo $this->getService()->confirm(Yii::$app->request->queryParams);
        Yii::$app->end(200);
    }

    // END Bitcoin actions

    // WU actions

    public function actionWu()
    {
        $service = $this->getService(Payment::WU_ID);

        Yii::$app->test->show($service->getInfoView());
    }

    public function actionWuaddpayment()
    {
        $this->validateAjax();

        $service = $this->getService(Payment::WU_ID);
        $form = $service->getForm();

        if($form->load($this->getPost()) && $form->save()){
            $this->ajax('_wuPaymentSubmit', ['message' => Yii::t('payment', 'Платёж отправлен на рассмотрение администратору')]);
        }

        $this->ajaxError($form->getErrors());
    }

    // END WU actions

    /**
     * @param null $name
     * @return \app\components\PaymentServices\Service
     */
    private function getService($name = null)
    {
        if($this->_service === null){
            $this->_service = Yii::$app->payment->getService($name);
        }

        return $this->_service;
    }

    private function setService($name)
    {
        $this->_service = Yii::$app->payment->getService($name);
    }
}