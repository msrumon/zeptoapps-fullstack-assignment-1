<?php

use App\Core\Router;

require_once implode(
    DIRECTORY_SEPARATOR,
    [dirname(__DIR__), 'vendor', 'autoload.php']
);

set_error_handler(
    function (int $errno, string $errstr, string $errfile, string $errline) {
        throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
    }
);

define(
    'APP_LOG_FILE',
    implode(
        DIRECTORY_SEPARATOR,
        [dirname(__DIR__), 'app.log']
    ),
);

$router = new Router();

$router->addRoute('GET', '/', 'UiController@main');
$router->addRoute('GET', '/font', 'FontController@list');
$router->addRoute('POST', '/font/create', 'FontController@persist');
$router->addRoute('POST', '/font/delete', 'FontController@destroy');
$router->addRoute('GET', '/group', 'GroupController@list');
$router->addRoute('POST', '/group/create', 'GroupController@persist');
$router->addRoute('POST', '/group/update', 'GroupController@modify');
$router->addRoute('POST', '/group/delete', 'GroupController@destroy');

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
