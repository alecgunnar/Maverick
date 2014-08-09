<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

use Maverick\View\IndexView;

require './vendor/autoload.php';

$app = new Maverick\Application();

$app->router->match('*', '/', function() {
    return IndexView::render();
});

$app->finish();