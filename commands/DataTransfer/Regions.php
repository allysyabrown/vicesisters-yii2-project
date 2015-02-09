<?php

namespace app\commands\DataTransfer;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 17.11.2014
 * Time: 12:08
 *
 * @property string $id
 * @property string $name
 *
 */
class Regions extends Model
{
    public static function tableName()
    {
        return 'regions';
    }
} 