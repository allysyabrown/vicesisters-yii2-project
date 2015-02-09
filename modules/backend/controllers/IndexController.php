<?php

namespace app\modules\backend\controllers;

use Yii;
use app\abstracts\BackController;

class IndexController extends BackController
{
    public function actionIndex()
    {
        return $this->render(['name' => 'Admin']);
    }
}
