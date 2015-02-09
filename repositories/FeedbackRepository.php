<?php

namespace app\repositories;

use app\components\Cache;
use app\models\Feedback;
use Yii;
use app\abstracts\Repository;
use app\abstracts\Notification;
use app\forms\FeedbackForm;
use app\models\User;
use app\models\Escort;
use app\components\FastData;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 24.11.2014
 * Time: 16:03
 */
class FeedbackRepository extends Repository
{
    const FEEDBACK_PREVIEW_LEN = 60;

    public function getFeedbackList()
    {
        $query = Feedback::find()
                    ->limit(Yii::$app->params['feedbackListLimit'])
                    ->orderBy('id DESC')
                    ->with('escort');

//        $feedbackList = Yii::$app->dbCache->getAll($query, Yii::$app->params['feedbackListCacheTime']);
        $feedbackList = $query->all();

        foreach($feedbackList as $feedback){
            $feedback->text = $this->getTextPreview($feedback->text);
        }

        return array_reverse($feedbackList);
    }

    public function getLastFeedback($lastId)
    {
        $lastId = (int)$lastId;
        if(!$lastId)
            return null;

        $lastId++;

        $query = Feedback::find()
            ->where(['id' => $lastId]);

        $feedback = Yii::$app->dbCache->getOne($query, Yii::$app->params['lastFeedbackCacheTime']);

        if($feedback)
            $feedback->text = $this->getTextPreview($feedback->text);

        return $feedback;
    }

    public function addFeedback(FeedbackForm $form)
    {
        $this->entity->setAttributes($form->getAttributes());
        $this->entity->beforeSave(null);
        if(!$this->entity->escort_id)
            $this->entity->escort_id = $form->escortId;

        $attributes = array_diff_assoc($this->entity->getAttributes(), ['id' => null]);

        $user = Yii::$app->data->getRepository('Account')->findEntityById($this->entity->user_id);
        if(!$user){
            $form->addError(null, Yii::t('error', 'Не удалось найти пользователя ID {account_id}', ['account_id' => $this->entity->user_id]));
            return false;
        }

        $attributes['avatar'] = $user->getAva();
        $attributes['name'] = $user->getUserName();
        $this->entity->setAttributes($attributes);

        if(!$this->entity->save()){
            $form->addError(null, Yii::t('error', 'Не удалось добавить отзыв'));
            return false;
        }

        $content = [
            'id' => $this->entity->id,
            'type' => Notification::RECORD_TYPE_FEEDBACK,
        ];

        Notification::addAnswerNewRecord($this->entity->escort_id, $content);

        $query = Feedback::find()
            ->where(['escort_id' => $this->entity->escort_id])
            ->orderBy('id DESC');

        Yii::$app->dbCache->update($query, Yii::$app->params['maxCacheTime']);

        return $this->entity;
    }

    /**
     * @param $id
     * @return \app\models\Feedback[]|null
     */
    public function findByEscortId($id)
    {
        $id = (int)$id;
        if(!$id)
            return null;


        $query = Feedback::find()
            ->where(['escort_id' => $id])
            ->orderBy('id DESC');

        $feedbacks = Yii::$app->dbCache->getAll($query, Yii::$app->params['maxCacheTime']);

        return $feedbacks;
    }

    public function getMineFeedback($escortId)
    {
        $query = Feedback::find()->where([
            'escort_id' => $escortId,
            'user_id' => Yii::$app->user->id
        ]);

        $feedback = Yii::$app->dbCache->getOne($query);

        return $feedback != null;
    }

    private function getTextPreview($text)
    {
        return strlen($text) > static::FEEDBACK_PREVIEW_LEN ? mb_substr($text, 0, static::FEEDBACK_PREVIEW_LEN, 'UTF-8').'...' : $text;
    }
}