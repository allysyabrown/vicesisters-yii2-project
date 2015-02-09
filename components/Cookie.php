<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\web\Cookie as YiiCookie;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 02.12.2014
 * Time: 10:57
 */
class Cookie extends Component
{
    const AGE_COOKIE_NAME = 'vicesisters_user_age';

    /**
     * @var \yii\web\CookieCollection
     */
    public $cookies;

    /**
     * @var integer
     */
    public $time;

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return Yii::$app->request->cookies->has($name);
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function set($name, $value)
    {
        Yii::$app->response->cookies->add(new YiiCookie([
            'name' => $name,
            'value' => $value
        ]));
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        return Yii::$app->request->cookies->getValue($name);
    }
} 