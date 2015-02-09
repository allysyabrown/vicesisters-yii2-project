<?php
/**
 * Created by JetBrains PhpStorm.
 * User: JanGolle
 * Date: 01.12.14
 * Time: 13:40
 * To change this template use File | Settings | File Templates.
 */

namespace app\modules\frontend\controllers;

use Yii;
use app\abstracts\FrontController;
use app\components\FastData;
use app\forms\TicketForm;
use app\forms\TicketMessageForm;
use app\models\Ticket;
use yii\web\ForbiddenHttpException;

class TicketController extends FrontController {

    public function actionIndex()
    {
        $ticketForm = new TicketForm();

        $ticketList = \Yii::$app->data->getRepository('Ticket')->getAllByUser();

        return $this->render([
            'ticketForm' => $ticketForm,
            'ticketList' => $ticketList,
        ]);
    }

    public function actionAdd()
    {
        $this->validateAjax();

        $ticketForm = new TicketForm();

        if($ticketForm->load($this->getPost())){
            $ticket = $ticketForm->save();
            if(!$ticket)
                $this->ajaxError($ticketForm->getErrors());

        } else {
            $this->ajaxError($ticketForm->getErrors());
        }
    }

    public function actionOpen($id)
    {
        $ticket = \Yii::$app->data->getRepository('Ticket')->findById($id);
        $ownerId = $ticket->account_id;

        if(\Yii::$app->user->id != $ownerId)
            throw new ForbiddenHttpException(\Yii::t('error', 'В доступе отказано'));

        \Yii::$app->fastData->removeFromString(FastData::KEY_NEW_USER_TICKET.$ownerId, $id);

        $ticketMessageForm = new TicketMessageForm();
        $ticketMessageForm->ticket_id = $id;

        return $this->render([
            'ticket' => $ticket,
            'ticketMessageForm' => $ticketMessageForm,
        ]);
    }

    public function actionSend()
    {
        $this->validateAjax();

        $ticketMessageForm = new TicketForm();

        if($ticketMessageForm->load($this->getPost())){
            $ticketMessage = $ticketMessageForm->save();
            if(!$ticketMessage)
                $this->ajaxError($ticketMessageForm->getErrors());
        } else {
            $this->ajaxError($ticketMessageForm->getErrors());
        }
    }

    public function actionUsertoadminform()
    {
        $this->validateAjax();

        $ticketForm = new TicketForm();

        $this->ajax('ticketForm', [
            'ticketForm' => $ticketForm,
        ]);
    }

    public function actionSendticket()
    {
        $this->validateAjax();

        $ticketForm = new TicketForm();

        if(!$ticketForm->load($this->getPost()))
            $this->ajaxError('Неверные данные');

        $ticketForm->category_id = 123;

        if($ticketForm->save())
            $this->ajax('_ticketSubmitMessage', ['message' => Yii::t('ajax', 'Ваше сообщение отправлено. Как только мы обработаем его, Вам придёт ответ на Ваш email')]);
        else
            $this->ajaxError($ticketForm->getErrors());
    }

}