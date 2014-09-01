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

$app = new Maverick\Application();

$app->start();

$app->router->match('*', '/', function() {
    return IndexView::render();
}, ['name' => 'home']);

$app->finish();