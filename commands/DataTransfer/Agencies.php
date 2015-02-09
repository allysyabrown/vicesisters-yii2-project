<?php

namespace app\commands\DataTransfer;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 17.11.2014
 * Time: 12:22
 *
 * @property string $id
 * @property string $login
 * @property string $password
 * @property string $agency_name
 * @property string $agency_description
 * @property string $credits
 * @property string $email
 * @property string $web
 * @property string $city
 * @property string $country
 * @property string $state
 * @property string $region
 * @property string $street_address
 * @property string $agency_phone
 *
 */
class Agencies extends Model
{
    public static function tableName()
    {
        return 'agencies';
    }
} 