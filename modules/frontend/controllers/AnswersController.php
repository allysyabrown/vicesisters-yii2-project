<?php

namespace app\modules\frontend\controllers;

use Yii;
use \app\abstracts\FrontController;
use app\abstracts\Notification;

/**
 * Created by JetBrains PhpStorm.
 * User: jangolle
 * Date: 08.12.14
 * Time: 15:59
 * To change this template use File | Settings | File Templates.
 */
class AnswersController extends FrontController
{

    public function actionIndex()
    {
        $answers = Notification::getAnswerMessages();

        Notification::readAll(Notification::FIELD_ANSWERS);

        //Yii::$app->test->show($answers);

        return $this->render([
            'answers' => $answers,
            'mainWrapperStyle' => 'background-image: url('.Yii::$app->user->getBigAva().'); background-size: 100%',
        ]);
    }

}