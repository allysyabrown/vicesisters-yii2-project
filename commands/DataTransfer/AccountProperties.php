<?php

namespace app\commands\DataTransfer;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 17.11.2014
 * Time: 11:46
 *
 * @property string $account_id
 * @property string $properties
 *
 */
class AccountProperties extends Model
{
    public static function tableName()
    {
        return 'account_properties';
    }
} 