<?php

namespace app\commands\DataTransfer;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 17.11.2014
 * Time: 11:40
 *
 * @property string $account_id
 * @property string $ts
 * @property string $views
 * @property string $popularity
 *
 */
class AccountStats extends Model
{
    public static function tableName()
    {
        return 'account_stats';
    }
} 