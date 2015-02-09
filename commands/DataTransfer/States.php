<?php

namespace app\commands\DataTransfer;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 17.11.2014
 * Time: 12:06
 *
 * @property string $id
 * @property string $sign
 * @property string $name
 *
 */
class States extends Model
{
    public static function tableName()
    {
        return 'states';
    }
} 