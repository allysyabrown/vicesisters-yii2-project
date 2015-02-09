<?php

namespace app\modules\frontend\controllers;

use Yii;
use app\abstracts\FrontController;
use app\forms\ChatForm;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 24.11.2014
 * Time: 13:44
 */
class ChatController extends FrontController
{
    public function actionIndex()
    {
        $form = new ChatForm();

        return $this->render([
            'form' => $form,
            'iframeUrl' => Yii::$app->chat->getIframeUrl(),
        ]);
    }
}