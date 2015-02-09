<?php

namespace app\commands\DataTransfer;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 17.11.2014
 * Time: 12:19
 *
 * @property string $id
 * @property string $country
 * @property string $state
 * @property string $name
 *
 */
class Cities extends Model
{
    public static function tableName()
    {
        return 'cities';
    }
} 