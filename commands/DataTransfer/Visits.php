<?php

namespace app\commands\DataTransfer;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 17.11.2014
 * Time: 12:05
 *
 * @property string $id
 * @property string $lastvisit
 *
 */
class Visits extends Model
{
    public static function tableName()
    {
        return 'visits';
    }
} 