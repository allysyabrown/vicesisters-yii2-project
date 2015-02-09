<?php

namespace app\components;

use Yii;
use yii\redis\Cache as RedisCache;

/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 30.10.2014
 * Time: 19:17
 */
class Cache extends RedisCache
{
    const VIP_ACCOUNT_KEY = 'cache_top_vip_escorts';
    const PREMIUM_ACCOUNT_KEY = 'cache_vip_escorts';
    const TOP_PROFILES_KEY = 'cache_top_profiles_escorts';
    const PROFILES_KEY = 'cache_profiles_escorts';
    const LAST_VERIFIED_KEY = 'cache_last_verified';
    const ESCORT_MESSAGE_KEY = 'cache_escort_message';
    const ESCORT_LAST_MESSAGE_KEY = 'cache_escort_last_message';
    const MERGED_PROFILES_KEY = 'cache_merged_profiles';
    const MERGED_PREMIUM_ACCOUNT_KEY = 'cache_merged_vips';
    const ESCORT_PHOTO_GALLERY_KEY = 'cache_escort_photo_gallery';
    const ESCORT_PHOTO_KEY = 'cache_escort_photo_id';
    const FEEDBACK_LIST_KEY = 'cache_feedback_list';
    const FEEDBACK_LAST_KEY = 'cache_feedback_last_id';
    const ESCORT_FEEDS_LIST_KEY = 'cache_escort_feeds_list';

    private static $_keys = [
        self::VIP_ACCOUNT_KEY,
        self::PREMIUM_ACCOUNT_KEY,
        self::TOP_PROFILES_KEY,
        self::PROFILES_KEY,
        //self::LAST_VERIFIED_KEY,
        //self::ESCORT_MESSAGE_KEY,
        //self::ESCORT_LAST_MESSAGE_KEY,
        self::MERGED_PROFILES_KEY,
        self::MERGED_PREMIUM_ACCOUNT_KEY,
        self::ESCORT_PHOTO_GALLERY_KEY,
        self::ESCORT_PHOTO_KEY,
        //self::FEEDBACK_LIST_KEY,
        //self::FEEDBACK_LAST_KEY,
        self::ESCORT_FEEDS_LIST_KEY,
        //FastData::HOT_MESSAGE_KEY,
        //FastData::HOT_MESSAGE_INCR_KEY,
        //FastData::FEED_MESSAGES_INCR_KEY,
        //FastData::FEEDBACK_KEY,
        //FastData::FEEDBACK_INCR_KEY,
        //FastData::PRIVATE_MESSAGES_INCR_KEY,
        //FastData::FEED_MESSAGES_KEY,
        //FastData::FEED_COMMENT_INCR_KEY,
        //FastData::FEED_COMMENTS_KEY,
        //FastData::IMAGE_LIKES_KEY,
        //FastData::USER_LIKES_KEY,
        //FastData::FEED_LIKES_KEY,
        FastData::ACCOUNT_PHOTOS_KEY,
        FastData::ACCOUNT_AVATAR_KEY,
        //FastData::KEY_NEW_ADMIN_TICKET,
        //FastData::KEY_NEW_USER_TICKET,
        //FastData::KEY_ANSWERS,
        //FastData::USER_NOTIFICATION_KEY,
        FastData::ESCORT_LIST_OFFSET,
    ];

    public function clear()
    {
        foreach(self::$_keys as $key){
            $subKeys = (array)Yii::$app->fastData->findAllKeys($key);
            if(empty($subKeys))
                continue;

            foreach($subKeys as $subKey){
                Yii::$app->fastData->del($subKey);
            }
        }
    }
 }