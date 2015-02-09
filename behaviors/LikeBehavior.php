<?php

namespace app\behaviors;

use Yii;
use app\helpers\Like;
use app\abstracts\Behavior;
use app\abstracts\Notification;
use app\components\FastData;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 01.12.2014
 * Time: 17:23
 *
 * @property \app\models\FeedMessage | \app\models\EscortPhoto $owner
 *
 */
class LikeBehavior extends Behavior
{
    const FEED_MESSAGE_NAME = 'app\models\FeedMessage';
    const IMAGE_NAME = 'app\models\EscortPhoto';
    const USER_IMAGE_LIKES_NAME = 'escort_image';
    const USER_FEED_LIKES_NAME = 'feed_message';

    public function events()
    {
        return [];
    }


    // Public function

    public function getUserLikes($id = null)
    {
        if($id === null)
            $id = Yii::$app->user->id;

        $data = (string)Yii::$app->fastData->get(FastData::USER_LIKES_KEY.':'.$id);
        $data = json_decode($data, true);

        if(!$data){
            $data = [
                static::USER_IMAGE_LIKES_NAME => '',
                static::USER_FEED_LIKES_NAME => '',
            ];
        }

        return $data;
    }

    /**
     * @return bool|integer
     */
    public function add()
    {
        // Пользователь не может лайкать свои сущности
        if($this->owner->getOwnerId() == Yii::$app->user->id)
            return null;

        $content['id'] = $this->owner->id;

        // Определим, с какой сущностью имеем дело
        if(!is_object($this->owner))
            return false;
        $className = get_class($this->owner);

        switch($className){
            case static::IMAGE_NAME:
                $like = $this->getImageLike();
                $likesKey = FastData::IMAGE_LIKES_KEY;
                $userLikesName = static::USER_IMAGE_LIKES_NAME;
                $paramCount = Yii::$app->params['escortImageLikeCount'];
                $content['type'] = Notification::LIKE_TYPE_PHOTO;
                break;
            case static::FEED_MESSAGE_NAME:
                $like = $this->getFeedMessageLike();
                $likesKey = FastData::FEED_LIKES_KEY;
                $userLikesName = static::USER_FEED_LIKES_NAME;
                $paramCount = Yii::$app->params['escortFeedLikeCount'];
                $content['type'] = Notification::LIKE_TYPE_FEED;
                break;
            default:
                return false;
        }

        $userId = Yii::$app->user->id;
        $id = $this->owner->id;

        // Добавим/удалим лайк сущности (в редисе к ключу лайков этой сущности добавится/удалится ID юзера)
        Yii::$app->fastData->set($likesKey.':'.$id, $this->parseLikeString($userId, $like));

        // Лайки пользователя (массив строк с айдишниками сущностей)
        $userLikes = $this->getUserLikes();

        // Добавим/удалим лайк пользователю (в редисе к ключу лайков юзера добавится/удалится ID сущности)
        $userLikes[$userLikesName] = $this->parseLikeString($id, $userLikes[$userLikesName]);
        Yii::$app->fastData->set(FastData::USER_LIKES_KEY.':'.$userId, json_encode($userLikes));

        if($this->isLiked()){
            // Уведомим пользователя о том, что мы лайкнули его контент
            Notification::addAnswerLike($this->owner->getOwnerId(), $content);

            // Увеличим рейтинг пользователя за то, что кто-то лайкнул его сущность
            Yii::$app->rating->add($this->owner->getOwnerId(), $paramCount);
        }else{
            Yii::$app->rating->add($this->owner->getOwnerId(), -$paramCount);
        }

        return $this->count();
    }

    public function count()
    {
        if(!is_object($this->owner))
            return false;
        $className = get_class($this->owner);
        switch($className){
            case static::FEED_MESSAGE_NAME:
                return $this->feedMessageLikesCount();
            case static::IMAGE_NAME:
                return $this->imageLikesCount();
            default:
                return false;
        }
    }

    public function isLiked()
    {
        if(!is_object($this->owner))
            return false;
        $className = get_class($this->owner);
        switch($className){
            case static::FEED_MESSAGE_NAME:
                return $this->isFeedMessageLiked();
            case static::IMAGE_NAME:
                return $this->isImageLiked();
            default:
                return false;
        }
    }

    // END Public function


    // Escort images likes

    public function imageLikesCount()
    {
        return Like::imageLikesCount($this->owner->id);
    }

    public function isImageLiked()
    {
        return Like::isImageLiked($this->owner->id);
    }

    private function getImageLike()
    {
        return Like::getImageLike($this->owner->id);
    }

    // END Escort images likes


    // Feed messages likes

    public function feedMessageLikesCount()
    {
        return Like::feedMessageLikesCount($this->owner->id);
    }

    public function isFeedMessageLiked()
    {
        return Like::isFeedMessageLiked($this->owner->id);
    }

    private function getFeedMessageLike()
    {
        return Like::getFeedMessageLike($this->owner->id);
    }

    // END Feed messages likes


    // Private functions

    /**
     * @param integer $id
     * @param string $like
     * @return string
     */
    private function parseLikeString($id, $like)
    {
        return Like::parseLikeString($id, $like);
    }

    // Private functions
}