<?php

namespace app\components;

use Yii;
use yii\base\Component;

/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 30.10.2014
 * Time: 20:04
 */
class FastData extends Component
{
    const HOT_MESSAGE_KEY = 'hot_message_key';
    const HOT_MESSAGE_INCR_KEY = 'hot_message_incr';
    const FEED_MESSAGES_INCR_KEY = 'escort_feed_message_incr';
    const FEEDBACK_KEY = 'feedback';
    const FEEDBACK_INCR_KEY = 'feedback_incr';
    const PRIVATE_MESSAGES_INCR_KEY = 'private_message_incr';
    const FEED_MESSAGES_KEY = 'escort_feed_messages';
    const FEED_MESSAGES_LIST_KEY = 'escort_feed_messages_list';
    const FEED_COMMENT_INCR_KEY = 'escort_feed_messages_comment_incr';
    const FEED_COMMENTS_KEY = 'escort_feed_message_comments';
    const IMAGE_LIKES_KEY = 'image_likes';
    const USER_LIKES_KEY = 'user_likes';
    const FEED_LIKES_KEY = 'feed_message_likes';
    const ACCOUNT_PHOTOS_KEY = 'escort_photo_';
    const ACCOUNT_AVATAR_KEY = 'account_ava_';
    const USER_REGION_SETTINGS_KEY = 'user_region_settings';
    const USER_PROPLAN_DISCOUNT_KEY = 'user_proplan_discount_price';
    const RANDOM_PHOTO_KEY = 'random_escort_photo';
    const VIPS_LIST_KEY = 'top_vips_escorts_list';

    const KEY_NEW_ADMIN_TICKET = 'new-admin-tickets';
    const KEY_NEW_USER_TICKET = 'new-user-tickets:';

    const KEY_ANSWERS = 'answers:';

    const USER_NOTIFICATION_KEY = 'user-notification:';

    const ESCORT_LIST_OFFSET = 'escort_list_offset';

    /**
     * @var \app\components\Redis
     */
    private $dataSource;

    public function init()
    {
        $this->dataSource = Yii::$app->redis;
    }

    public function get($key)
    {
        return $this->dataSource->get(trim($key));
    }

    public function set($key, $value)
    {
        return $this->dataSource->set($key, $value);
    }

    public function incr($key){
        return $this->dataSource->incr($key);
    }

    public function del($key){
        return $this->dataSource->del($key);
    }

    public function hget($key,$field){
        return $this->dataSource->hget($key,$field);
    }

    public function hincr($key,$field,$by = 1){
        $this->dataSource->hincrby($key,$field,$by);
    }

    public function hvals($key){
        return $this->dataSource->hvals($key);
    }

    public function hset($key,$field,$value){
        $this->dataSource->hset($key,$field,$value);
    }

    public function hdel($key,$field){
        return $this->dataSource->hdel($key,$field);
    }

    public function lpush($key,$value){
        return $this->dataSource->lpush($key,$value);
    }

    public function lrange($key, $start = 0, $end = 20){
        return $this->dataSource->lrange($key,$start,$end);
    }

    public function ltrim($key, $start = 0, $end = 10){
        return $this->dataSource->ltrim($key,$start,$end);
    }

    public function expire($key,$time){
        return $this->dataSource->expire($key,$time);
    }

    public function remove($key)
    {
        $this->dataSource->remove($key);
    }

    public function findAll($key)
    {
        return $this->dataSource->findAll($key);
    }

    public function getOnlineEscorts(){
        return $this->dataSource->getOnlineEscorts();
    }

    public function getOnlineCount()
    {
        return count($this->dataSource->getOnlineEscortKeys());
    }

    /**
     * @param string $key
     * @param $needle
     * @return bool
     */
    public function haveInString($key, $needle)
    {
        $string = $this->dataSource->get($key);
        if(!$string)
            return false;

        $needle = $this->delim($needle);

        return strpos($string, $needle) !== false;
    }

    /**
     * @param string $key
     * @param $value
     * @return bool
     */
    public function addToString($key, $value)
    {
        if($this->haveInString($key,$value))
            return false;

        $dValue = $this->delim($value);

        $string = $this->dataSource->get($key);
        if(!$string){
            $this->dataSource->set($key,$dValue);
            return true;
        }

        $string .= $dValue;
        $this->dataSource->set($key,$string);
        return true;
    }

    /**
     * @param string $key
     * @param $value
     * @return bool
     */
    public function removeFromString($key, $value)
    {
        $string = $this->dataSource->get($key);
        if(!$string)
            return false;

        $cutString = str_replace($this->delim($value),'',$string);
        $this->dataSource->set($key,$cutString);
        return true;
    }

    /**
     * @param $value
     * @return string
     */
    private function delim($value)
    {
        return ':'.$value.':';
    }

    public function getFeedMessage($id)
    {
        $data = $this->dataSource->findAll(static::FEED_MESSAGES_KEY.':*:'.$id);
        return $data ? $data[0] : null;
    }

    public function getFeedComment($id)
    {
        $data = $this->dataSource->findAll(static::FEED_COMMENTS_KEY.':*:'.$id);
        return $data ? $data[0] : null;
    }

    public function addFeedMessage(array $message, $escortId)
    {
        $incrKey = static::FEED_MESSAGES_INCR_KEY;
        $this->dataSource->incr($incrKey);
        $incr = $this->dataSource->get($incrKey);

        $message['id'] = $incr;
        return $this->dataSource->set(static::FEED_MESSAGES_KEY.':'.$escortId.':'.$incr, json_encode($message)) ? $incr : null;
    }

    public function addFeedback(array $feedback, $escortId)
    {
        $incrKey = static::FEEDBACK_INCR_KEY;
        $this->dataSource->incr($incrKey);
        $incr = $this->dataSource->get($incrKey);

        $feedback['id'] = $incr;
        return $this->dataSource->set(static::FEEDBACK_KEY.':'.$escortId.':'.$incr, json_encode($feedback)) ? $incr : null;
    }

    public function addFeedComment(array $message, $messageId)
    {
        $incrKey = static::FEED_COMMENT_INCR_KEY;
        $this->dataSource->incr($incrKey);
        $incr = $this->dataSource->get($incrKey);

        $message['id'] = $incr;

        return $this->dataSource->set(static::FEED_COMMENTS_KEY.':'.$messageId.':'.$incr, json_encode($message)) ? $incr : null;
    }

    public function findEscortFeeds($escortId)
    {
        return $this->findAll(static::FEED_MESSAGES_KEY.':'.$escortId);
    }

    public function findAllFeeds()
    {
        return $this->findAll(static::FEED_MESSAGES_KEY.':*');
    }

    public function findAllFeedComments()
    {
        return $this->findAll(static::FEED_COMMENTS_KEY.':*');
    }

    public function findFeedMessageComments($messageId)
    {
        return $this->findAll(static::FEED_COMMENTS_KEY.':'.$messageId);
    }

    public function findAllKeys($key)
    {
        return $this->dataSource->findAllKeys($key);
    }
}