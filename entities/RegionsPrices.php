<?php

namespace app\entities;

use Yii;
use app\abstracts\Entity;


/**
 * Created by PhpStorm.
 * User: rem
 * Date: 21.01.2015
 * Time: 14:55
 */
class RegionsPrices extends Entity
{
    public $region;
    public $country;
    public $state;
    public $city;
    public $price;
} 