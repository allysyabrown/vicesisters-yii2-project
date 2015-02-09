<?php

require(__DIR__.'/aliases.php');

$params = require(__DIR__.'/params.php');

$config = [
    'name' => 'Vicesisters',
    'id' => 'vicesisters',
    'sourceLanguage' => 'ru',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'modules' => require(__DIR__.'/modules.php'),
    'components' => require(__DIR__.'/components.php'),
    'controllerMap' => require(__DIR__.'/controllerMap.php'),
    'params' => $params,
];

if(YII_ENV_DEV){
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;