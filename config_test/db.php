<?php

return [
    'class' => 'app\components\DataBase',
    'dsn' => 'pgsql:host=db_server;port=5432;dbname=vicesisters_db',
    'username' => 'web',
    'password' => '051571',
    'charset' => 'utf8',
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 3600,
];
