<?php

return [
    'rabbit' => [
        'class' => 'webtoucher\amqp\controllers\AmqpListenerController',
        'interpreters' => [
            'my-exchange' => 'app\components\RabbitInterpreter', // interpreters for each exchange
        ],
        'exchange' => 'my-exchange', // default exchange
    ],
];