<?php

namespace app\modules\frontend\controllers;

use Yii;
use app\abstracts\FrontController;
use yii\web\NotFoundHttpException;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 24.11.2014
 * Time: 11:08
 */
class PhotoController extends FrontController
{
    public function actionIndex($id, $escortId)
    {
        $photo = $this->model('EscortPhoto')->findCashedById($id);

        if(!$photo)
            throw new NotFoundHttpException(Yii::t('error', 'Фотография не найдена'));

        $this->ajax('index', [
            'escortId' => $escortId,
            'photo' => $photo
        ]);
    }

    public function actionPrev($id, $escortId)
    {
        $photo = $this->model('EscortPhoto')->getPrevPhoto($id, $escortId);

        if(!$photo)
            throw new NotFoundHttpException(Yii::t('error', 'Фотография не найдена'));

        $this->ajax('index', [
            'escortId' => $escortId,
            'photo' => $photo
        ]);
    }

    public function actionNext($id, $escortId)
    {
        $photo = $this->model('EscortPhoto')->getNextPhoto($id, $escortId);

        if(!$photo)
            throw new NotFoundHttpException(Yii::t('error', 'Фотография не найдена'));

        $this->ajax('index', [
            'escortId' => $escortId,
            'photo' => $photo
        ]);
    }

    public function actionGallery($escortId)
    {
        $photos = $this->model('EscortPhoto')->getEscortPhotos($escortId);

        return $this->render([
            'escortId' => $escortId,
            'photos' => $photos,
        ]);
    }
}