<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');
$db_mysql = require(__DIR__.'/db_mysql.php');

return [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii'],
    'controllerNamespace' => 'app\commands',
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'components' => [
        'cache' => [
            'class' => 'app\components\Cache',
            'redis' => [
                'hostname' => 'redis_server',
                'port' => 6379,
                'database' => 0,
            ],
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'db_mysql' => $db_mysql,
        'amqp' => require(__DIR__.'/amqp.php'),
        'stats' => [
            'class' => 'app\components\Stats'
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => require(__DIR__.'/routing.php'),
        ],
        'fastData' => [
            'class' => 'app\components\FastData',
        ],
        'redis' => [
            'class' => 'app\components\Redis',
            'hostname' => 'redis_server',
            'port' => 6379,
            'database' => 0,
        ],
        'data' => [
            'class' => 'app\components\Data',
        ],
        'search' => [
            'class' => 'app\components\Search',
            'dsn' => 'mysql:host=vicesisters.test;port=9306;',
            //'dsn' => 'pgsql:host=vicesisters.test;port=5432;dbname=vicesisters_db',
            'username' => 'web',
            'password' => '051571',
        ],
        'user' => [
            'class' => '\app\components\User',
            'identityClass' => '\app\models\Account',
            'authTimeout' => 3600*24,
            'enableAutoLogin' => true,
        ],
        'local' => [
            'class' => 'app\components\Local',
        ],
        'rating' => [
            'class' => 'app\components\Rating',
        ],
        'dbCache' => [
            'class' => 'app\components\DbCache'
        ],
        'cookie' => [
            'class' => 'app\components\Cookie'
        ],
        'static' => [
            'class' => 'app\components\StaticData',
            'defaultHostId' => 1
        ],
        'image' => [
            'class' => 'app\components\Image',
        ],
        'ftp' => [
            'class' => 'app\components\FtpManager',
            'user' => 'vice_ftp',
            'password' => '051571',
        ],
    ],
    'params' => $params,
];
