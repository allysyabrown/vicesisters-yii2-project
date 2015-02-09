<?php

namespace app\modules\backend\controllers;

use Yii;
use app\abstracts\BackController;
use app\models\Transaction;
use app\models\Payment;
use yii\helpers\Url;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 24.12.2014
 * Time: 10:01
 */
class PaymentController extends BackController
{
    public function actionAlltransactions()
    {
        return $this->render('list', [
            'attributes' => Transaction::dataAttributes(),
            'ajaxUrl' => Url::toRoute(['payment/alltranactionsajax']),
            'pageName' => Yii::t('back', 'Список транзакций'),
        ]);
    }

    public function actionAlltranactionsajax()
    {
        $this->validateAjax();

        $transaction = $this->model('Transaction')->findByAjax($this->get());

        $this->ajax($transaction);
    }

    public function actionAllpayments($status = null)
    {
        return $this->render('list', [
            'attributes' => Payment::dataAttributes(),
            'ajaxUrl' => $status === null ? Url::to(['payment/allpaymentsajax']) : Url::to(['payment/allpaymentsajax', 'status' => $status]),
            'pageName' => Yii::t('back', 'Список платежей'),
            'tableFilteringSettings' => Payment::getPaymentListItems($status),
        ]);
    }

    public function actionAllpaymentsajax($status = null)
    {
        $this->validateAjax();

        if($status === null)
            $transaction = $this->model('Payment')->findByAjax($this->get());
        else
            $transaction = $this->model('Payment')->findByAjax($this->get(), ['where' => ['status' => $status]]);

        $this->ajax($transaction);
    }

    public function actionClosepayment($id)
    {
        $this->validateAjax();

        if($this->model('Payment')->submit($id)){
            $this->ajax(Yii::t('back', 'Платёж подтверждён'));
        }else{
            $this->ajaxError('Не удалось подтвердить платёж');
        }
    }

    public function actionRejectpayment($id)
    {
        $this->validateAjax();

        if($this->model('Payment')->reject($id)){
            $this->ajax(Yii::t('back', 'Платёж отклонён'));
        }else{
            $this->ajaxError('Не удалось отклонить платёж');
        }
    }
}