<?php

namespace app\commands\DataTransfer;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 17.11.2014
 * Time: 12:00
 *
 * @property string $id
 * @property string $account_id
 * @property string $ts
 * @property string $region
 * @property string $country
 * @property string $state
 * @property string $city
 * @property string $days
 *
 */
class TopVips extends Model
{
    public static function tableName()
    {
        return 'top_vips';
    }
} 