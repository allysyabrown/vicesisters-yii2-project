<?php

namespace app\modules\backend\controllers;

use Yii;
use app\abstracts\BackController;
use app\models\Ticket;
use yii\helpers\Url;
use app\components\AjaxData;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 24.12.2014
 * Time: 19:02
 */
class TicketController extends BackController
{
    public function actionAll()
    {
        return $this->render('list', [
            'ajaxUrl' => Url::toRoute('ticket/allajax'),
            'attributes' => Ticket::dataAttributes([]),
        ]);
    }

    public function actionOpened()
    {
        return $this->render('list', [
            'ajaxUrl' => Url::toRoute('ticket/openedajax'),
            'attributes' => Ticket::dataAttributes(['closeButton']),
        ]);
    }

    public function actionClosed()
    {
        return $this->render('list', [
            'ajaxUrl' => Url::toRoute('ticket/closedajax'),
            'attributes' => Ticket::dataAttributes([]),
        ]);
    }

    public function actionAllajax()
    {
        $this->validateAjax();

        $tickets = $this->model('Ticket')->findByAjax($this->get(), [
            'with' => ['account', 'ticketMessages'],
        ], []);

        $this->ajax($tickets);
    }

    public function actionOpenedajax()
    {
        $this->validateAjax();

        $tickets = $this->model('Ticket')->findByAjax($this->get(), [
            'with' => ['account', 'ticketMessages'],
            'where' => ['status' => Ticket::STATUS_OPENED],
        ], ['closeButton']);

        $this->ajax($tickets);
    }

    public function actionClosedajax()
    {
        $this->validateAjax();

        $tickets = $this->model('Ticket')->findByAjax($this->get(), [
            'with' => ['account', 'ticketMessages'],
            'where' => ['status' => Ticket::STATUS_CLOSED],
        ], []);

        $this->ajax($tickets);
    }

    public function actionClose($id)
    {
        $this->validateAjax();

        if($this->model('Ticket')->changeStatus($id, Ticket::STATUS_CLOSED))
            $this->ajax('Тикет закрыт');
        else
            $this->ajaxError('Не удалось закрыть тикет');
    }
}