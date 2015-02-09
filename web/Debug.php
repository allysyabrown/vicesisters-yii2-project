<?php
/**
 * Created by PhpStorm.
 * User: rem
 * Date: 12.12.2014
 * Time: 12:14
 */

class Debug
{
    private static $_time;

    public static function setTime()
    {
        self::$_time = time();
    }

    public static function time()
    {
        echo self::$_time;
        echo '<br />';
        echo time();
        echo '<br />';
        echo time() - self::$_time;
        exit();
    }

    public static function show($something, $isDump = false)
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