[![SensioLabsInsight](https://insight.sensiolabs.com/projects/2976dc02-a08c-4199-ba3a-c1d1dc6af9e5/big.png)](https://insight.sensiolabs.com/projects/2976dc02-a08c-4199-ba3a-c1d1dc6af9e5)

# Simple Router #

Simple routing to the simple actions.

## How to use? - Example ##

It is simpler than you think:

```php

<?php

use Router\Router;

$router = new Router();

// set default data
$router->setDefaultModule('index');
$router->setDefaultController('index');
$router->setDefaultAction('index');

// set first track

// you can always set your request type, so you can define four the same url addresses with different request

$router->add(
    '/',
    [
        'module' => Namespace1\Namespace2\Namespace3,
        'controller' => 'Controller',
        'action' => 'action'
    ],
    [
        'method' => get|post
    ]
);

//you can always set data from url address

// param1 values will change on :param values from url, if the value meets the criteria.

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

```