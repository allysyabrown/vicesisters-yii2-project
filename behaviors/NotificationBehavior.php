<?php

namespace app\behaviors;


use app\abstracts\Behavior;
use app\abstracts\Notification;

/**
 * Created by JetBrains PhpStorm.
 * User: jangolle
 * Date: 04.12.14
 * Time: 17:18
 * To change this template use File | Settings | File Templates.
 *
 * @property \app\models\FeedMessage | \app\models\Message $owner
 *
 */
class NotificationBehavior extends Behavior {

    public function notify()
    {
        Notification::add(Notification::getField($this->owner->className()), $this->owner->getNotifiedId());
    }

}