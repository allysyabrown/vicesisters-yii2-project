<?php

namespace app\components;

use Yii;
use yii\redis\Connection;
use app\models\Escort;

/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 30.10.2014
 * Time: 19:14
 */
class Redis extends Connection
{
    // Redis commands
    const COMMAND_KEYS = 'KEYS';
    const COMMAND_MGET = 'MGET';
    const COMMAND_SORT = 'SORT';
    const COMMAND_LPUSH = 'LPUSH';
    const COMMAND_INCREMENT = 'INCR';
    const COMMAND_HMSET = 'HMSET';
    const COMMAND_SADD = 'SADD';
    const COMMAND_GET = 'GET';

    public function remove($key)
    {
        $key = trim($key);
        if($key){
            $this->executeCommand('DEL', [$key]);
        }
    }

    /*public function getActiveSessions()
    {
        $keys = $this->getActiveSessionKeys();
        if(!is_array($keys) || count($keys) === 0)
            return [];

        $values = [];
        foreach($keys as $key){
            $values[$key] = $this->get($keys[0]);
        }

        return $values;
    }*/

    public function getActiveSessionKeys()
    {
        return $this->executeCommand(static::COMMAND_KEYS, [\Yii::$app->session->keyPrefix.'*']);
    }

    public function getOnlineEscortKeys()
    {
        return $this->executeCommand(static::COMMAND_KEYS, [Escort::ONLINE_KEY.'*']);
    }

    public function getOnlineEscorts(){
        if($this->getOnlineEscortKeys())
            return $this->executeCommand(static::COMMAND_MGET, $this->getOnlineEscortKeys());
        return false;
    }

    public function findAll($key)
    {
        $keys = $this->findAllKeys($key);
        if(!$keys)
            return null;

        /**
         * todo Избавиться от костыля
         */
        $res = [];
        foreach($keys as $k){
            $res[] = $this->get($k);
        }
        return $res;
        //return $this->executeCommand(static::COMMAND_MGET, $keys);
    }

    public function findAllKeys($key)
    {
        return $this->executeCommand(static::COMMAND_KEYS, [$key.'*']);
    }
}