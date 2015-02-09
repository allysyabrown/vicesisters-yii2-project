<?php

namespace app\repositories;

use app\models\Membership;
use Yii;
use app\forms\HotMessageForm;
use app\components\FastData;
use app\entities\Dialog;
use app\abstracts\Repository;
use app\models\Message;
use app\entities\HotMessage;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 19.11.2014
 * Time: 14:42
 */
class MessageRepository extends Repository
{
    public function addHotMessage(HotMessageForm $form)
    {
        $id = $this->fastData->get(FastData::HOT_MESSAGE_INCR_KEY) + 1;
        $this->fastData->incr(FastData::HOT_MESSAGE_INCR_KEY);

        $data = $form->img;
        if(!$data){
            $form->addError('img', Yii::t('error', 'Неверные данные'));
            return false;
        }

        $form->img = Yii::$app->image->uploadImageFromSource($data);

        $params = $form->getAttributes();
        $params['id'] = $id;

        $proplan = Yii::$app->data->getRepository('Membership')->findById(Membership::HOT_MESSAGE);
        $time = $proplan && $proplan->duration ? 3600*$proplan->duration : Yii::$app->params['hotMessageLifeTime'];

        $this->fastData->set(FastData::HOT_MESSAGE_KEY.':'.$id, json_encode($params));
        $this->fastData->expire(FastData::HOT_MESSAGE_KEY.':'.$id, $time);

        $result = Yii::$app->user->payForHotMessage($id);
        if(!$result){
            $form->addError('img', Yii::t('error', 'Не удалось произвести оплату. Проверьте состояние счёта'));
            $this->fastData->remove(FastData::HOT_MESSAGE_KEY.':'.$id);
        }

        return $result;
    }

    public function createPrivateMessage(array $params)
    {
        $this->entity->setAttributes($params);
        $this->entity->save(false);

        $this->entity->notify();

        return $this->entity;
    }

    /**
     * @param $id
     * @return \app\entities\HotMessage|null
     */
    public function getLastHotMessage($id)
    {
        //version in which we have just 4 messages without any loops
        $id = (int)$id;
        $id++;
        $data = $this->fastData->get(FastData::HOT_MESSAGE_KEY.':'.$id);

        return $data ? $this->createHotMessage(json_decode($data, true)) : null;
    }

    /**
     * @return null|\app\entities\HotMessage[]
     */
    public function getHotMessages()
    {
        $messages = [];
        $messagesData = $this->fastData->findAll(FastData::HOT_MESSAGE_KEY);

        if($messagesData){
            foreach($messagesData as $data){
                $message = $this->createHotMessage(json_decode($data, true));
                $messages[$message->id] = $message;
            }

            ksort($messages);
        }

        return $messages;
    }

    /**
     * @param array $attributes
     * @return \app\Models\Message
     */
    private function createHotMessage(array $attributes)
    {
        $message = new HotMessage();
        $message->setAttributes($attributes);

        return $message;
    }

    /*todo GOVNOKOD, HARDCODE*/
    public function getDialogs()
    {
        $userId = Yii::$app->user->id;

        $sql = 'SELECT MAX(date) as date,dialog_code FROM message WHERE ("from" = :user OR "to" = :user) GROUP BY dialog_code ORDER BY date DESC';
        $messages = Message::findBySql($sql,[':user' => $userId])->all();

        $dialogs = [];

        foreach($messages as $message){
//            $lastMessage = $this->getPgArray($message->text)[0];
//            $endString = '';
//
//            if(strlen($lastMessage) > 64)
//                $endString = '...';

            $dialogs[] = Dialog::load([
                'dialogCode' => $message->dialog_code,
                'lastMessage' => '',
//                'lastMessage' => mb_substr($lastMessage, 0, 64, 'UTF-8').$endString,
                'lastDate' => $message->date,
                'subscriber' => $this->getSubscriber($message),
            ]);
        }

        return $dialogs;
    }

    public function getPrivateMessagesByOpponent($id)
    {
        $userId = Yii::$app->user->id;
        $sql = 'SELECT * FROM message WHERE ("from" = :user OR "to" = :user) AND ("from" = :subscriber OR "to" = :subscriber) AND type = :type ORDER BY date ASC';

        return Message::findBySql($sql,[
            ':user' => $userId,
            ':subscriber' => $id,
            ':type' => Message::PRIVATE_MESSAGE,
        ])->all();
    }

    public function getPrivateMessagesByCode($code)
    {
        $sql = 'SELECT * FROM message WHERE "dialog_code" = :code AND type = :type ORDER BY date ASC';

        return Message::findBySql($sql,[
            ':code' => $code,
            ':type' => Message::PRIVATE_MESSAGE,
        ])->all();
    }

    public function getOpponentByCode($code)
    {
        return abs(intval(str_replace(Yii::$app->user->id, '', $code)));
    }

    public function readNewMessages($messages)
    {
        $countNew = $this->countNewMessages($messages);
        if($countNew === 0)
            return false;

        foreach($messages as $msg){
            $msg->read();
        }
//        Notification::read(Notification::FIELD_MESSAGES, Yii::$app->user->id, $countNew);
    }

    private function countNewMessages($messages)
    {
        $counter = 0;
        foreach($messages as $msg){
            if($msg->isNew)
                $counter++;
        }

        return $counter;
    }

    private function getSubscriber(Message $message){
        $subscriberId = str_replace('-', '', str_replace(Yii::$app->user->id, '', $message->dialog_code));
        return Yii::$app->data->getRepository('Account')->findEntityById($subscriberId);
    }
}