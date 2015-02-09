<?php
/**
 * Created by JetBrains PhpStorm.
 * User: JanGolle
 * Date: 18.11.14
 * Time: 11:42
 * To change this template use File | Settings | File Templates.
 */

namespace app\components;

use webtoucher\amqp\components\AmqpInterpreter;


class RabbitInterpreter extends AmqpInterpreter
{
    /**
     * Interprets AMQP message with routing key 'hello_world'.
     *
     * @param array $message
     */
    public function readHelloWorld($message)
    {
        // todo: write message handler
        $this->log(print_r($message, true));
    }
}