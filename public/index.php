<?php

use Maverick\Application;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Response;

require dirname(__DIR__) . '/vendor/autoload.php';

$app = new Maverick\Application();

$app->initialize();

$app(ServerRequest::fromGlobals(), new Response());
