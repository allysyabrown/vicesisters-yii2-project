<?php

namespace app\modules\frontend\controllers;

use Yii;
use app\abstracts\FrontController;
use yii\web\BadRequestHttpException;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 29.12.2014
 * Time: 18:30
 */
class RedirectController extends FrontController
{
    public function actionIndex($page)
    {
        $page = urldecode($page);
        if(!$page || strpos($page, '.') === false)
            throw new BadRequestHttpException(Yii::t('error', 'Неверный запрос'));

        if(strpos($page, 'http://') === false)
            $page = 'http://'.$page;

        return $this->render([
            'page' => $page,
            'notShowShit' => true,
            'notShowFuckingShit' => true,
        ]);
    }
}