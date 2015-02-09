<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jangolle
 * Date: 04.12.14
 * Time: 11:05
 * To change this template use File | Settings | File Templates.
 */

namespace app\abstracts;

use Yii;
use app\components\FastData;
use app\models\Account;

/**
 * Class Notification
 * @package app\components
 *
 * @property Account $user
 * @property int $messages
 * @property int $feeds
 * @property int $favorites
 * @property int $answers
 */
 abstract class Notification extends BaseModel
{
    const FIELD_MESSAGES = 'msg';
    const FIELD_FEEDS = 'feed';
    const FIELD_FAVORITES = 'fav';
    const FIELD_ANSWERS = 'answ';

    const ANSWERS_LENGTH = 20;

    const ANSWER_TYPE_LIKE = 'like';
    const ANSWER_TYPE_NEW_RECORD = 'record';
    const ANSWER_TYPE_FAV = 'fav';

    const RECORD_TYPE_FEED = 'feed';
    const RECORD_TYPE_COMMENT = 'comment';
    const RECORD_TYPE_FEEDBACK = 'feedback';

    const LIKE_TYPE_PHOTO = 'photo';
    const LIKE_TYPE_FEED = 'feed';

    /**
     * @var array
     */
    public static $_counters = [
        self::FIELD_MESSAGES => 0,
        self::FIELD_FEEDS => 0,
        self::FIELD_FAVORITES => 0,
        self::FIELD_ANSWERS => 0,
    ];

    public static $_dependencies = [
        'app\models\FeedMessage' => self::FIELD_FEEDS,
        'app\models\Message' => self::FIELD_MESSAGES,
    ];

    /**
     * @var int
     */
    public static $total = 0;

    public static function run()
    {
        self::fillCounters();
    }

    /**
     * @return int
     */
    public static function getMessages()
    {
        return (integer)self::$_counters[self::FIELD_MESSAGES];
    }

    /**
     * @return int
     */
    public static function getFeeds()
    {
        return (integer)self::$_counters[self::FIELD_FEEDS];
    }

    /**
     * @return int
     */
    public static function getFavorites()
    {
        return (integer)self::$_counters[self::FIELD_FAVORITES];
    }

    /**
     * @return int
     */
    public static function getAnswers()
    {
        return (integer)self::$_counters[self::FIELD_ANSWERS];
    }

    public static function getTotal()
    {
        return self::$total;
    }

    public static function getKey()
    {
        return FastData::USER_NOTIFICATION_KEY . \Yii::$app->user->id;
    }

    public static function getField($className)
    {
        return self::$_dependencies[$className];
    }

    public static function fillCounters()
    {
        foreach(self::$_counters as $field => $value){
            self::$_counters[$field] = (integer)\Yii::$app->fastData->hget(self::getKey(), $field);
            self::$total += self::$_counters[$field];
        }
    }

     public static function getAnswerMessages()
     {
         $key = FastData::KEY_ANSWERS.Yii::$app->user->id;

         $messages = Yii::$app->fastData->lrange($key, 0 ,self::ANSWERS_LENGTH);

         $userIdsArray = [];

         if($messages){
             foreach($messages as $key => $message){
                 $mes = json_decode($message);

                 $messages[$key] = $mes;
                 $userIdsArray[] = $mes->user_id;
             }
         }

         return $messages;
     }

     public static function add($field, $userId, $by = 1)
     {
         if(!$userId)
             return false;

         Yii::$app->fastData->hincr(FastData::USER_NOTIFICATION_KEY.$userId, $field, $by);
     }

     public static function addAnswerLike($userId, array $content)
     {
         $key = FastData::KEY_ANSWERS.$userId;

         $answer = [];

         $answer['type'] = self::ANSWER_TYPE_LIKE;
         $answer['user_id'] = isset($content['user_id']) ? $content['user_id'] : Yii::$app->user->id;
         $answer['user_role'] = isset($content['user_role']) ? $content['user_role'] : Yii::$app->user->getRole();
         $answer['time'] = (new \DateTime())->format(\Yii::$app->params['dateTimeFormat']);
         unset($content['user_id'], $content['user_role']);
         $answer['content'] = $content;

         switch($content['type']){
             case self::LIKE_TYPE_PHOTO:
                 $answer['render'] = '_likePhoto';
                 break;
             case self::LIKE_TYPE_FEED:
                 $answer['render'] = '_likeFeed';
                 break;
             default:
                 $answer['render'] = '_likeFeed';
         }

         self::pushAnswer($key, json_encode($answer));
         self::add(self::FIELD_ANSWERS, $userId);
     }

     public static function addAnswerNewRecord($userId, $content)
     {
         $ownerId = isset($content['user_id']) ? $content['user_id']: Yii::$app->user->id;
         if($ownerId == $userId)
             return false;

         $key = FastData::KEY_ANSWERS.$userId;

         $answer['type'] = self::ANSWER_TYPE_NEW_RECORD;
         $answer['user_id'] = isset($content['user_id']) ? $content['user_id'] : Yii::$app->user->id;
         $answer['user_role'] = isset($content['user_role']) ? $content['user_role'] : Yii::$app->user->getRole();
         $answer['time'] = (new \DateTime())->format(\Yii::$app->params['dateTimeFormat']);
         unset($content['user_id'], $content['user_role']);
         $answer['content'] = $content;

         switch($content['type']){
             case self::RECORD_TYPE_FEED:
                 $answer['render'] = '_recordFeed';
                 break;
             case self::RECORD_TYPE_COMMENT:
                 $answer['render'] = '_recordComment';
                 break;
             case self::RECORD_TYPE_FEEDBACK:
                 $answer['render'] = '_recordFeedback';
                 break;
             default:
                 $answer['render'] = '_recordFeed';
         }

         self::pushAnswer($key, json_encode($answer));
         self::add(self::FIELD_ANSWERS, $userId);
     }

     public static function addAnswerFavorite($userId, $content = [])
     {
         $key = FastData::KEY_ANSWERS.$userId;

         $answer['type'] = self::ANSWER_TYPE_FAV;
         $answer['user_id'] = isset($content['user_id']) ? $content['user_id'] : Yii::$app->user->id;
         $answer['user_role'] = isset($content['user_role']) ? $content['user_role'] : Yii::$app->user->getRole();
         $answer['time'] = (new \DateTime())->format(\Yii::$app->params['dateTimeFormat']);
         $answer['render'] = '_favorite';

         self::pushAnswer($key, json_encode($answer));
         self::add(self::FIELD_ANSWERS, $userId);
     }

     private static function pushAnswer($key, $answer)
     {
         $len = self::ANSWERS_LENGTH - 1;
         Yii::$app->fastData->lpush($key, $answer);
         Yii::$app->fastData->ltrim($key, 0, $len);
     }

     public static function readOne($field, $userId)
     {
         if((bool)Yii::$app->fastData->hget(FastData::USER_NOTIFICATION_KEY.$userId, $field)){
             Yii::$app->fastData->hincr(FastData::USER_NOTIFICATION_KEY.$userId, $field, -1);
             self::$total -= 1;
         }
     }

     public static function read($field, $userId, $amount)
     {
         if(Yii::$app->fastData->hget(FastData::USER_NOTIFICATION_KEY.$userId, $field) >= $amount){
             Yii::$app->fastData->hincr(FastData::USER_NOTIFICATION_KEY.$userId, $field, -$amount);
             self::$total -= $amount;
         } else {
             Yii::$app->fastData->hdel(FastData::USER_NOTIFICATION_KEY.$userId, $field);
             self::$total -= $amount;
         }
     }

     public static function readAll($field, $userId = null)
     {
         if($userId === null)
             $userId = Yii::$app->user->id;

         Yii::$app->fastData->hdel(FastData::USER_NOTIFICATION_KEY.$userId, $field);
     }

}