<?php

namespace app\commands\DataTransfer;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 17.11.2014
 * Time: 12:00
 *
 * @property string $id
 * @property string $country
 * @property string $state
 *
 */
class TravelList extends Model
{
    public static function tableName()
    {
        return 'travel_list';
    }
} 