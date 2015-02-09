<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\helpers\Json;

/**
 * Created by PhpStorm.
 * User: rem
 * Date: 25.11.2014
 * Time: 11:23
 *
 * @property \app\components\Redis $redis
 */
class Chat extends Component
{
    const PROTOCOL = 'http://';
    const NEW_MESSAGE_URL = 'addmessage';
    const IFRAME_URL = 'iframe';
    const CHAT_USER_REDIS_KEY = 'vs_chat_user';

    /**
     * @var \app\components\Redis
     */
    private $_redis;

    /**
     * @var string
     */
    public $baseUrl;

    /**
     * @var string
     */
    public $redisServer;

    /**
     * @var string
     */
    public $salt;

    public function getNewMessageUrl()
    {
        return $this->createUrl(static::NEW_MESSAGE_URL);
    }

    public function createUrl($url = null)
    {
        $userId = Yii::$app->getUser()->getId();

        $baseUrl = static::PROTOCOL.$this->baseUrl;
        $url = $url ? $baseUrl.'/'.$url : $baseUrl;
        $url .= Yii::$app->user->getIsGuest() ? '' : '/user/'.$userId.'/hash/'.$this->getHash($userId);
        $url .= '/lang/'.Yii::$app->local->lang;
        return $url;
    }

    public function getIframeUrl()
    {
        return $this->createUrl(static::IFRAME_URL);
    }

    public function addUser($id, array $info)
    {
        $info['hash'] = $this->getHash($id);

        $this->getRedis()->set(static::CHAT_USER_REDIS_KEY.':'.$id, Json::encode($info));
    }

    public function removeUser($id)
    {
        $this->getRedis()->del(static::CHAT_USER_REDIS_KEY.':'.$id);
    }

    /**
     * @return \app\components\Redis
     */
    public function getRedis()
    {
        if($this->_redis === null){
            $this->_redis = new Redis();
            $this->_redis->hostname = $this->redisServer;
        }

        return $this->_redis;
    }

    private function getHash($userId)
    {
        $hash = crypt($userId, '$6$rounds=5120$'.$this->salt.'$');
        return str_replace('/', '', $hash);
    }
}