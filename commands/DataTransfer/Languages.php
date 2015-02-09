<?php

namespace app\commands\DataTransfer;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 17.11.2014
 * Time: 11:51
 *
 * @property string $id
 * @property string $sign
 * @property string $name
 * @property string $onsite
 *
 */
class Languages extends Model
{
    public static function tableName()
    {
        return 'languages';
    }
} 