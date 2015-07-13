<?php

namespace Router;

require_once 'vendor/autoload.php';

$router = new Router();

$router->setDefaultModule('index');
$router->setDefaultController('index');
$router->setDefaultAction('index');

$router->add(
    '/admin/controllers/user/show/:param',
    [
        'module' => 'Lib\Controllers',
        'controller' => 'user',
        'action' => 'show',
        'param1' => 1
    ],
    [
        'method' => 'get'
    ]
);

$router->add(
    '/',
    [
        'controller' => 'index',
        'action' => 'index',
    ],
    [
        'method' => 'get | post'
    ]
);

$router->add(
    '/admin/:controller/:action',
    [
        'controller' => 1,
        'action' => 2
    ],
    [
        'method' => 'post'
    ]
);

$router->add(
    '/admin/controllers/user/show/:param/:param',
    [
        'module' => 'Controllers',
        'controller' => 'user',
        'action' => 'show',
        'param1' => 1,
        'param2' => 2
    ],
    [
        'method' => 'get'
    ]
);

$router->add(
    '/admin/:module/users/show/:param',
    [
        'module' => 1,
        'controller' => 'users',
        'action' => 'show',
        'param1' => 1
    ],
    [
        'method' => 'get'
    ]
);

$router->add(
    '/admin/users/show/:name',
    [
        'controller' => 'users',
        'action' => 'show',
        'name1' => 1
    ],
    [
        'method' => 'get'
    ]
);

$router->add(
    '/admin/cont/:action/:param/:param',
    [
        'controller' => 'cont',
        'action' => 1,
        'param1' => 2,
        'param2' => 3
    ],
    [
        'method' => "get | post",
    ]
);

$route = $router->resolve();

foreach ($route as $key => $value) {
    echo $key.': ';
    if (is_string($value)) {
        echo $value;
    } elseif (is_array($value)) {
        foreach ($value as $key2 => $value2) {
            echo '<br><span style="margin-left: 50px"><b>'.$key2.': '.$value2.'</b></span><br>';
        }
    }
    echo '<br>';
}
