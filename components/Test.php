<?php

namespace app\components;

use yii\base\Component;

/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 30.10.2014
 * Time: 19:55
 */
class Test extends Component
{
    public function show($something, $isDump = false)
    {
        echo '<pre>';
        if($isDump)
            var_dump($something);
        else
            print_r($something);
        echo '</pre>';
        die();
    }
} 