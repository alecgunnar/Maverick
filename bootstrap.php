<?php

use Maverick\Application;

define('ROOT', __DIR__ . '/');

require(__DIR__ . '/vendor/autoload.php');

Application::setDebugLevel(Application::DEBUG_LEVEL_TEST);