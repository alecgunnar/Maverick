<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

use Maverick\Application,
    Maverick\View\IndexView;

define('ROOT', dirname(__DIR__) . '/');

require ROOT . 'vendor/autoload.php';

Application::setDebugLevel(Application::DEBUG_LEVEL_DEV);

$app = new Maverick\Application();

$app->start();

$app->router->match('*', '/', function() {
    return IndexView::render();
}, ['name' => 'home']);

$app->finish();