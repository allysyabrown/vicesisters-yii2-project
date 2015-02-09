<?php

namespace app\commands\DataTransfer;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 17.11.2014
 * Time: 12:03
 *
 * @property string $account_id
 * @property string $ts
 * @property string $month_counts
 *
 */
class Vips extends Model
{
    public static function tableName()
    {
        return 'vips';
    }
} 