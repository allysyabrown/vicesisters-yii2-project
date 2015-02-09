<?php

namespace app\commands\DataTransfer;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 17.11.2014
 * Time: 11:47
 *
 * @property string $account_id
 * @property string $agency_id
 *
 */
class AccountsToAgencies extends Model
{
    public static function tableName()
    {
        return 'accounts_to_agencies';
    }
} 