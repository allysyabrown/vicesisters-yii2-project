<?php

namespace app\modules\frontend\controllers;

use Yii;
use app\abstracts\FrontController;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 01.12.2014
 * Time: 17:11
 */
class LikeController extends FrontController
{
    public function actionFeedmessage($id)
    {
        $this->validateAjax();

        if(Yii::$app->user->isGuest)
            $this->ajaxError(Yii::t('error', 'Не удалось совершить операцию'));

        $message = $this->model('FeedMessage')->getById($id);

        if(!$message)
            $this->ajaxError(Yii::t('error', 'Не удалось найти новость ID {feed_id}', ['feed_id' => $id]));

        if($message->getBehavior('like')->add() === null)
            $this->ajaxError(Yii::t('error', 'Не удалось совершить операцию'));

        $message->likesCount = $message->likes;

        $this->ajax('escort/_feedMessageLikes', [
            'message' => $message,
        ]);
    }

    public function actionEscortphoto()
    {
        $this->validateAjax();

        if(Yii::$app->user->isGuest)
            $this->ajaxError(Yii::t('error', 'Не удалось совершить операцию'));

        $photo = $this->model('EscortPhoto')->getById($this->getPost('escortPhotoId'));

        if(!$photo)
            $this->ajaxError(Yii::t('error', 'Не удалось найти фото ID {photo_id}', ['photo_id' => $this->getPost('escortPhotoId')]));

        if($photo->like->add() === null)
            $this->ajaxError(Yii::t('error', 'Не удалось совершить операцию'));

        $this->ajax('escort/_escortPhotoLike', [
            'photo' => $photo,
        ]);
    }
} 