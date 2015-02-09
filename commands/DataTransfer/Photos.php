<?php

namespace app\commands\DataTransfer;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 17.11.2014
 * Time: 11:56
 *
 * @property string $id
 * @property string $name
 * @property string $type
 * @property string $created
 * @property string $status
 * @property string $nudity
 * @property string $moderated
 * @property string $popularity
 * @property string $views
 *
 */
class Photos extends Model
{
    public static function tableName()
    {
        return 'photos';
    }
}