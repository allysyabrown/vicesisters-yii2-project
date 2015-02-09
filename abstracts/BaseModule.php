<?php
/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 06.10.14
 * Time: 20:10
 */

namespace app\abstracts;

use yii\base\Module;

abstract class BaseModule extends Module
{
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
} 