<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jangolle
 * Date: 05.12.14
 * Time: 15:08
 * To change this template use File | Settings | File Templates.
 */

namespace app\entities;

use app\abstracts\Entity;

class Dialog extends Entity
{
    public $dialogCode;

    public $lastDate;

    public $lastMessage;

    public $subscriber;
}