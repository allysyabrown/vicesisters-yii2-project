<?php

namespace app\commands\DataTransfer;


/**
 * Created by PhpStorm.
 * User: rem
 * Date: 17.11.2014
 * Time: 18:56
 *
 * @property string $id
 * @property string $sign
 * @property string $region
 * @property string $name
 * @property string $pref
 * @property string $sms
 *
 */
class Countries extends Model
{
    public static function tableName()
    {
        return 'countries';
    }
} 