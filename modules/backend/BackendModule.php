<?php

namespace app\modules\backend;

use app\abstracts\BaseModule;

class BackendModule extends BaseModule
{
    public $controllerNamespace = 'app\modules\backend\controllers';

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
