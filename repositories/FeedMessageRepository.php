<?php

namespace app\repositories;

use Yii;
use app\models\Account;
use app\models\EscortInfo;
use app\helpers\Like;
use app\models\Host;
use app\models\User;
use app\components\FastData;
use app\forms\FeedMessageCommentForm;
use app\forms\FeedMessageForm;
use app\models\Escort;
use app\models\FeedMessage;
use app\models\FeedMessageComment;
use app\abstracts\Repository;
use app\abstracts\Notification;
use yii\db\Query;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 27.11.2014
 * Time: 17:22
 */
class FeedMessageRepository extends Repository
{
    public function getById($id)
    {
        $id = (int)$id;
        if(!$id)
            return null;

        $data = $this->fastData->getFeedMessage($id);
        if($data){
            $data = json_decode($data, true);

            $this->entity->setAttributes($data);
            $this->entity->id = $data['id'];

            return $this->entity;
        }else{
            return $this->entity->findCachedOne($id);
        }
    }

    public function getCommentById($id)
    {
        $id = (int)$id;
        if(!$id)
            return null;

        $data = $this->fastData->getFeedComment($id);
        if($data){
            $data = json_decode($data, true);

            $comment = new FeedMessageComment();

            $comment->id = $data['id'];
            $comment->feedMessageId = $data['feedMessageId'];
            $comment->ownerId = $data['ownerId'];
            $comment->title = $data['title'];
            $comment->text = $data['text'];
            $comment->date = $data['date'];

            return $comment;
        }else{
            return FeedMessageComment::findCachedOne($id);
        }
    }

    /**
     * @param FeedMessageForm $form
     * @return bool|\app\models\FeedMessage
     */
    public function addFeedMessage(FeedMessageForm $form)
    {
        $owner = Yii::$app->data->getRepository('Account')->findEntityById($form->ownerId);
        if(!$owner){
            $form->addError(null, Yii::t('error', 'Не удалось найти пользователя ID {id}', ['id' => $form->ownerId]));
            return false;
        }

        $attributes = [
            'ownerId' => $form->ownerId,
            'ownerFullName' => $owner->getFullName(),
            'date' => Yii::$app->local->dateTime(),
            'text' => $form->text,
            'ownerHomeUrl' => $owner->getIsEscort() ? Url::toRoute(['escort/profile', 'id' => $form->ownerId]) :  Url::toRoute(['user/profile', 'id' => $form->ownerId]),
            'escort_id' => $form->escortId,
            'owner_id' => $form->ownerId,
        ];

        $id = $this->fastData->addFeedMessage($attributes, $form->escortId);
        $attributes['id'] = $id;

        /**
         * todo Реализовать с помощью событий
         */
        $this->entity->setAttributes($attributes);
        $this->entity->id = $id;
        $this->entity->notify();
        $content = [
            'id' => $id,
            'type' => Notification::RECORD_TYPE_FEED,
        ];

        Notification::addAnswerNewRecord($form->ownerId, $content);

        return $attributes;
    }

    public function addFeedComment(FeedMessageCommentForm $form)
    {
        $owner = Yii::$app->data->getRepository('Account')->findEntityById($form->ownerId);
        if(!$owner){
            $form->addError(null, Yii::t('error', 'Не удалось найти пользователя ID {id}', ['id' => $form->ownerId]));
            return false;
        }

        $attributes = [
            'ownerAvatar' => $owner->getAvatar(),
            'ownerFullName' => $owner->getFullName(),
            'date' => Yii::$app->local->dateTime(),
            'text' => $form->text,
            'ownerHomeUrl' => $owner->getIsEscort() ? Url::toRoute(['escort/profile', 'id' => $form->ownerId]) :  Url::toRoute(['user/profile', 'id' => $form->ownerId]),
            'title' => $form->title,
            'feedMessageId' => $form->feedMessageId,
            'ownerId' => $form->ownerId,
        ];

        $id = Yii::$app->fastData->addFeedComment($attributes, $form->feedMessageId);
        $attributes['id'] = $id;

        /**
         * todo Реализовать с помощью событий
         */
        $content = [
            'id' => $id,
            'type' => Notification::RECORD_TYPE_COMMENT,
        ];

        Notification::addAnswerNewRecord($form->escortId, $content);

        return $attributes;
    }

    /**
     * @param int $id
     * @param int $offset
     * @return \app\models\FeedMessage[]
     */
    public function findByEscortId($id, $offset = 0)
    {
        $id = (int)$id;
        if(!$id)
            return null;

        $limit = Yii::$app->params['escortFeedsLimit'];

        $key = FastData::FEED_MESSAGES_LIST_KEY.':'.$id.':'.$offset;
        $oldMessages = Yii::$app->cache->get($key);

        $roleQuery = Account::find()
            ->select(['role'])
            ->where(['id' => $id]);

        $role = Yii::$app->dbCache->get($roleQuery, Yii::$app->params['maxCacheTime'])->asArray()->one();

        if($role == Account::ROLE_ADMIN)
            return null;

        $isUser = $role == Account::ROLE_USER;

        if(!$oldMessages){
            if($isUser === true){
                $ownerFullName =
                    'CASE
                        WHEN "owner"."first_name" IS NOT NULL
                            THEN "owner"."first_name"||\' \'||"owner"."last_name"
                        ELSE "user"."user_name"
                    END AS "ownerFullName"';
            }else{
                $ownerFullName = '"user"."user_name" AS "ownerFullName"';
            }

            $commentOwnerFullName = 'CASE
                                        WHEN "comment_user"."user_name" IS NOT NULL
                                            THEN "comment_user"."user_name"
                                        WHEN "comment_info"."first_name" IS NOT NULL
                                            THEN "comment_info"."first_name"||\' \'||"comment_info"."last_name"
                                        ELSE "comment_escort"."user_name"
                                    END AS "commentOwnerFullName"';

            $commentOwnerAvatar = 'CASE
                                        WHEN "comment_user"."avatar" IS NOT NULL
                                            THEN \'http://\'||("host"."name")||\'/\'||"comment_user"."avatar"
                                        ELSE
                                            \'http://\'||("host"."name")||\'/\'||"comment_escort"."avatar"
                                    END AS "commentOwnerAvatar"';

            $query = (new Query())->select([
                'message.id',
                'message.owner_id AS ownerId',
                'message.escort_id AS escortId',
                $ownerFullName,
                '\'http://\'||("host"."name")||\'/\'||"user"."avatar" AS "ownerAvatar"',
                'message.date',
                'message.text',
                'comment.id AS commentId',
                'comment.owner_id AS commentOwnerId',
                $commentOwnerFullName,
                'comment_account.role AS commentOwnerRole',
                $commentOwnerAvatar,
                'comment.date AS commentDate',
                'comment.text AS commentText',
            ])
                ->from(FeedMessage::tableName().' message')
                ->where(['message.escort_id' => $id])
                ->leftJoin(FeedMessageComment::tableName().' comment', '"comment"."feed_message_id" = "message"."id"')
                ->leftJoin(Account::tableName(), '"account"."id" = "message"."owner_id"')
                ->leftJoin(Account::tableName().' comment_account', '"comment_account"."id" = "comment"."owner_id"')
                ->leftJoin(EscortInfo::tableName().' comment_info', '"comment_info"."id" = "comment"."owner_id"')
                ->leftJoin(Escort::tableName().' comment_escort', '"comment_escort"."id" = "comment"."owner_id"')
                ->leftJoin(User::tableName().' comment_user', '"comment_user"."id" = "comment"."owner_id"')
                ->leftJoin(Host::tableName(), '"host"."id" = '.Host::PRIMARY_HOST_ID)
                ->limit($limit)
                ->offset($offset)
                ->orderBy('message.id DESC');

            if($isUser === true){
                $query  ->leftJoin(User::tableName().' user', '"owner"."id" = "message"."owner_id"');
            }else{
                $query  ->leftJoin(EscortInfo::tableName().' owner', '"owner"."id" = "message"."owner_id"')
                    ->leftJoin(Escort::tableName().' user', '"user"."id" = "message"."owner_id"');
            }

            //\Debug::show([$query->createCommand()->sql, $query->createCommand()->params]);
            //\Debug::show($query->all());

            $oldMessages = $query->all();
            Yii::$app->cache->set($key, $oldMessages, Yii::$app->params['feedMessagesListCacheTime']);
        }

        //Yii::$app->fastData->findEscortFeeds()
        //Yii::$app->fastData->findFeedMessageComments()

        if(!empty($oldMessages))
            $limit -= count($oldMessages);

        $newMessages = [];

        if($limit > 0){
            $newMessagesJson = Yii::$app->fastData->findEscortFeeds($id);

            if($newMessagesJson){
                usort($newMessagesJson, function($a, $b){
                    $pattern = '/"id":"(\d+)"/';

                    $result = preg_match($pattern, $a, $aMatches);
                    if($result)
                        $result = preg_match($pattern, $b, $bMatches);

                    if(!$result)
                        return -1;

                    return $aMatches[1] > $bMatches[1] ? -1 : 1;
                });

                for($i = $offset; $i < $offset+$limit; $i++){
                    if(isset($newMessagesJson[$i])){
                        $newMessages[] = Json::decode($newMessagesJson[$i]);
                    }
                }
            }
        }

        $feeds = [];
        $messages = array_merge($newMessages, $oldMessages);

        if(!empty($messages)){
            foreach($messages AS $message){
                if(!isset($feeds[$message['id']])){
                    $ownerId = isset($message['ownerId']) ? $message['ownerId'] : $message['owner_id'];
                    $escortId = isset($message['escortId']) ? $message['escortId'] : $message['escort_id'];
                    $ownerName = isset($message['ownerFullName']) ? $message['ownerFullName'] : (isset($message['name']) ? $message['name'] : 'noname');
                    $ownerHomeUrl = $isUser === true ? Url::toRoute(['user/profile', 'id' => $ownerId]) : Url::toRoute(['escort/profile', 'id' => $ownerId]);


                    $newMessage = [
                        'id' => $message['id'],
                        'ownerId' => $ownerId,
                        'escortId' => $escortId,
                        'ownerFullName' => $ownerName,
                        'date' => $message['date'],
                        'text' => $message['text'],
                        'ownerHomeUrl' => $ownerHomeUrl,
                        'canLike' => Yii::$app->user->id != $ownerId ? 1 : 0,
                        'canComment' => Yii::$app->user->id != $ownerId ? 1 : 0,
                        'isLiked' => Like::isFeedMessageLiked($message['id']),
                        'likesCount' => Like::feedMessageLikesCount($message['id']),
                        'comments' => [],
                    ];

                    $feeds[$message['id']] = $newMessage;
                }

                if(isset($message['commentId']) && $message['commentId']){
                    $isCommentOwnerUser = $message['commentOwnerRole'] == Account::ROLE_USER;
                    $commentOwnerHomeUrl = $isCommentOwnerUser === true ? Url::toRoute(['user/profile', 'id' => $message['commentOwnerId']]) : Url::toRoute(['escort/profile', 'id' => $message['commentOwnerId']]);

                    $feeds[$message['id']]['comments'][] = [
                        'id' => $message['commentId'],
                        'ownerAvatar' => $message['commentOwnerAvatar'],
                        'ownerFullName' => $message['commentOwnerFullName'],
                        'date' => $message['date'],
                        'text' => $message['text'],
                        'ownerHomeUrl' => $commentOwnerHomeUrl,
                    ];
                }elseif($comments = Yii::$app->fastData->findFeedMessageComments($message['id'])){
                    foreach($comments as $comment){
                        if($comment){
                            $feeds[$message['id']]['comments'][] = Json::decode($comment);
                        }
                    }
                }
            }
        }

        return $feeds;
    }

    /**
     * @param integer $messageId
     * @return \app\models\FeedMessageComment[]
     */
    public function getMessageComments($messageId)
    {
        $comments = [];
        $commentsData = Yii::$app->fastData->findFeedMessageComments($messageId);

        if($commentsData){
            foreach($commentsData as $data){
                $comment = new FeedMessageComment();
                $data = json_decode($data, true);
                $comment->id = $data['id'];
                $comment->owner = Yii::$app->data->getRepository('Account')->findCashedById($data['ownerId']);
                $comment->text = $data['text'];
                $comment->date = $data['date'];

                $comments[$data['id']] = $comment;
            }

            ksort($comments);
        }

        return $comments;
    }

    public function getAllFeedsInFastData()
    {
        return $this->fastData->findAllFeeds();
    }

    public function getAllCommentsInFastData()
    {
        return $this->fastData->findAllFeedComments();
    }

    public function dumpFeed($id, $escortId)
    {
        if($this->getById($id)->save(true)) {
            $this->fastData->del(FastData::FEED_MESSAGES_KEY . ':' . $escortId . ':' . $id);
            return true;
        }
        return false;
    }

    public function dumpComment($id, $feedMessageId)
    {
        if($this->getCommentById($id)->save(false)){
            $this->fastData->del(FastData::FEED_COMMENTS_KEY . ':' . $feedMessageId . ':' . $id);
            return true;
        }
        return false;
    }
}