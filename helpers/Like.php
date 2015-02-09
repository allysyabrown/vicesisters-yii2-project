<?php

namespace app\helpers;

use app\components\FastData;
use Yii;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 05.02.2015
 * Time: 13:43
 */
class Like
{
    const LIKE_STRING_DELIMITER = ':';

    private static $_imageLike;
    private static $_feedLike;

    public static function feedMessageLikesCount($id)
    {
        $count = (int)substr_count(self::getFeedMessageLike($id), self::LIKE_STRING_DELIMITER);
        return $count/2;
    }

    public static function isFeedMessageLiked($id)
    {
        $del = static::LIKE_STRING_DELIMITER;
        $key = $del.Yii::$app->user->id.$del;
        return strpos(self::getFeedMessageLike($id), $key) !== false;
    }

    public static function imageLikesCount($id)
    {
        $count = (int)substr_count(self::getImageLike($id), self::LIKE_STRING_DELIMITER);
        return $count/2;
    }

    public static function isImageLiked($id)
    {
        $del = static::LIKE_STRING_DELIMITER;
        $key = $del.Yii::$app->user->id.$del;
        return strpos(self::getImageLike($id), $key) !== false;
    }

    public static function getImageLike($id)
    {
        return (string)Yii::$app->fastData->get(FastData::IMAGE_LIKES_KEY.':'.$id);
    }

    public static function getFeedMessageLike($id)
    {
        return (string)Yii::$app->fastData->get(FastData::FEED_LIKES_KEY.':'.$id);
    }

    public static function parseLikeString($id, $like)
    {
        $del = self::LIKE_STRING_DELIMITER;
        $id = $del.$id.$del;
        $like = (string)$like;

        if($like){
            if(strpos($like, $id) === false)
                $like .= $id;
            else
                $like = str_replace($id, '', $like);
        }else{
            $like = $id;
        }

        return $like;
    }
} 