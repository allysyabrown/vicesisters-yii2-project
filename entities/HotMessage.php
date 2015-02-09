<?php

namespace app\entities;

use Yii;
use app\abstracts\Entity;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 09.12.2014
 * Time: 12:06
 */
class HotMessage extends Entity
{
    public $id;
    public $owner;
    public $title;
    public $text;
    public $date;
    public $img;
} 