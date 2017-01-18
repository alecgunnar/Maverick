<?php

use GuzzleHttp\Psr7\ServerRequest;

// Define some paths and files

$root = dirname(__DIR__);
$autoloader = $root . '/vendor/autoload.php';
$cache = $root . '/cache';

// Check for the autoloader

if (!file_exists($autoloader)) {
    die('Please run `composer install` before atteming to load this example.');
}

// Get the autoloader

require $autoloader;

// Load the container and the application from it

$container = \Maverick\bootstrap($root);
$app = $container->get('application');

// Run the application

$response = $app->handleRequest(ServerRequest::fromGlobals());
$app->sendResponse($response);
