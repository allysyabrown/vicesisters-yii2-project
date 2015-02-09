<?php

return [
    'request' => [
        'class' => 'app\components\Request',
        'cookieValidationKey' => 'W00X-69Jv41TYizUjemnZ5o9bKxBCW2S',
    ],
    'cache' => [
        'class' => 'app\components\Cache',
        'redis' => [
            'hostname' => 'redis_server',
            'port' => 6379,
            'database' => 0,
        ],
    ],
    'session' => [
        'class' => 'app\components\Session',
        /*'redis' => [
            'hostname' => 'redis_server',
            'port' => 6379,
            'database' => 0,
        ],
        'keyPrefix' => 'session_'*/
    ],
    'user' => [
        'class' => '\app\components\User',
        'identityClass' => '\app\models\Account',
        'authTimeout' => 3600*24,
        'enableAutoLogin' => true,
    ],
    'errorHandler' => [
        'class' => 'yii\web\ErrorHandler',
        'errorAction' => 'frontend/site/error',
    ],
    'mailer' => [
        'class' => 'yii\swiftmailer\Mailer',
        'useFileTransport' => true,
    ],
    'log' => [
        'traceLevel' => YII_DEBUG ? 3 : 0,
        'targets' => [
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['error', 'warning'],
            ],
        ],
    ],
    'db' => require(__DIR__.'/db.php'),
    'db_mysql' => require(__DIR__.'/db_mysql.php'),
    'urlManager' => [
        'enablePrettyUrl' => true,
        'enableStrictParsing' => true,
        'showScriptName' => false,
        'rules' => require(__DIR__.'/routing.php'),
    ],
    'view' => [
        'renderers' => [
            'twig' => [
                'class' => 'app\components\Renderer',
                'cachePath' => null,
                'functions' => require(__DIR__.'/twig_functions.php'),
                'globals' => require(__DIR__.'/twig_globals.php'),
            ],
        ],
    ],
    'authManager' => [
        'class' => '',
    ],
    'i18n' => [
        'translations' => [
            'app*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'fileMap' => [
                    'app' => 'app.php',
                ],
            ],
            '*' => [
                'class' => 'app\components\MessageSource',
            ],
        ],
    ],
    'redis' => [
        'class' => 'app\components\Redis',
        'hostname' => 'redis_server',
        'port' => 6379,
        'database' => 0,
    ],
    'test' => [
        'class' => 'app\components\Test',
    ],
    'fastData' => [
        'class' => 'app\components\FastData',
    ],
    'search' => [
        'class' => 'app\components\Search',
        'dsn' => 'mysql:host=vicesisters.test;port=9306;',
        //'dsn' => 'pgsql:host=vicesisters.test;port=5432;dbname=vicesisters_db',
        'username' => 'web',
        'password' => '051571',
    ],
//    'sphinx' => [
//        'class' => 'yii\sphinx\Connection',
//        'dsn' => 'pgsql:host=vicesisters.test;port=5432;dbname=vicesisters_db',
//        'username' => 'web',
//        'password' => '051571',
//        'schemaMap' => [
//            'pgsql' => [
//                'class' => \yii\db\pgsql\Schema::className(),
//                'defaultSchema' => 'public'
//            ]
//        ]
//    ],
    'data' => [
        'class' => 'app\components\Data',
    ],
    'static' => [
        'class' => 'app\components\StaticData',
        'defaultHostId' => 1
    ],
    'rating' => [
        'class' => 'app\components\Rating',
    ],
    'payment' => [
        'class' => 'app\components\Payment',
        'systemCurrency' => 'USD',
        'services' => [
            'bitcoin' => [
                'remoteServiceUrl' => 'https://blockchain.info/',
                'secret' => 'klsdjfljKKJj789UUII7999DSF',
                'bitcoinAddress' => '12p2RqLXSKrsP68QprnHuJXJQszpQFfdyd', // 15kCvtosJ3ai35wK4a4bRjzLYXoSURqcTK
            ],
            'wu' => [

            ]
        ],
    ],
    'image' => [
        'class' => 'app\components\Image',
    ],
    'ftp' => [
        'class' => 'app\components\FtpManager',
        'user' => 'vice_ftp',
        'password' => '051571',
    ],
    'amqp' => require(__DIR__.'/amqp.php'),
    'stats' => [
        'class' => 'app\components\Stats'
    ],
    'chat' => [
        'class' => 'app\components\Chat',
        'baseUrl' => 'chat.vicesisters.test',
        'redisServer' => 'chat_redis_server',
        'salt' => 'IOKjOJlOLDFDSF3poL987UYi87yH',
    ],
    'cron' => [
        'class' => 'app\components\CronManager',
        'root' => 'C:\\Users\\JanGolle\\Documents\\',
    ],
    'cookie' => [
        'class' => 'app\components\Cookie'
    ],
    'dbCache' => [
        'class' => 'app\components\DbCache'
    ],
	'assetManager' => [
        'bundles' => [
             'yii\web\JqueryAsset' => [
                'sourcePath' => null,
                'js' => [] 
            ],        
        ],
    ],
    'local' => [
        'class' => 'app\components\Local',
    ],
    'ajaxData' => [
        'class' => 'app\components\AjaxData',
    ],
];