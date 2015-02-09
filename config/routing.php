<?php

function addRoutesPrefix($routes, $prefix)
{
    $indexedRoutes = [];
    foreach($routes as $name => $route){
        if($prefix == 'backend')
            $name = 'admin/'.$name;
        $indexedRoutes[$name] = $prefix.'/'.$route;
    }

    return $indexedRoutes;
}

$frontend = addRoutesPrefix(require(__DIR__.'/routing_front.php'), 'frontend');
$backend = addRoutesPrefix(require(__DIR__.'/routing_back.php'), 'backend');

return array_merge($frontend, $backend, [
    'admin' => 'backend/index/index',
    '/' => 'frontend/index/index',

    '<controller:\w+>/<id:\d+>' => '<controller>/index',
    '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
    '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
]);