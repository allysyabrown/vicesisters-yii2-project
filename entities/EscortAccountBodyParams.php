<?php

namespace app\entities;

use Yii;
use app\abstracts\Entity;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 05.12.2014
 * Time: 15:26
 */
class EscortAccountBodyParams extends Entity
{
    public $height;
    public $weight;
    public $eyes;
    public $hair;
    public $breast;
    public $waist;
    public $hips;
    public $ethnicity;
    public $orientation;
    public $bodyParams;
}