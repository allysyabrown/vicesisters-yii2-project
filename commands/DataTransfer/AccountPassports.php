<?php

namespace app\commands\DataTransfer;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 17.11.2014
 * Time: 11:42
 *
 * @property string $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $address
 * @property string $city
 * @property string $state
 * @property string $country
 * @property string $id_image
 * @property string $submitted
 * @property string $status
 *
 */
class AccountPassports extends Model
{
    public static function tableName()
    {
        return 'account_passports';
    }
} 