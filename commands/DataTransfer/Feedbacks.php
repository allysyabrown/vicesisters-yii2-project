<?php

namespace app\commands\DataTransfer;


/**
 * Created by PhpStorm.
 * User: rem
 * Date: 17.11.2014
 * Time: 11:32
 *
 * @property int $id
 * @property int $userid
 * @property int $subject
 * @property string $body
 * @property string $email
 * @property string $created
 * @property string $status
 * @property string $ip
 *
 */
class Feedbacks extends Model
{
    public static function tableName()
    {
        return 'feedbacks';
    }
} 