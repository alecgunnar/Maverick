<?php

use Maverick\Middleware\RouterMiddleware;
use Maverick\Router\FastRouteRouter;
use Maverick\Router\Collection\FastRouteRouteCollection;
use Maverick\Router\Loader\FileSystemRouteLoader;
use Maverick\Router\Loader\RouteLoader;
use Maverick\Handler\NotFoundHandler;
use Maverick\Handler\NotAllowedHandler;
use Relay\Middleware\ResponseSender as ResponseSenderMiddleware;

return [
    'system.route_collection' => function($c) {
        $collection = new FastRouteRouteCollection();

        $c->get('system.route_loader')
            ->loadRoutes($collection);

        return $collection;
    },
    'system.route_loader' => function($c) {
        return new FileSystemRouteLoader(
            $c->get('system.config.routes_file'),
            new RouteLoader($c)
        );
    },
    'system.router' => function($c) {
        $instance = new FastRouteRouter($c->get('system.fast_route.dispatcher'));

        return $instance->setNotFoundHandler($c->get('system.handler.not_found'))
            ->setNotAllowedHandler($c->get('system.handler.not_allowed'));
    },
    'system.fast_route.dispatcher' => function($c) {
        return \FastRoute\simpleDispatcher(
            $c->get('system.route_collection'),
            $c->get('system.fast_route.options')
        );
    },
    'system.fast_route.options' => function() {
        return [];
    },
    'system.config.routes_file' => function() {
        return __DIR__ . '/router.php';
    },
    'system.handler.not_found' => function() {
        return new NotFoundHandler();
    },
    'system.handler.not_allowed' => function() {
        return new NotAllowedHandler();
    },
    'system.middleware.router' => function($c) {
        return new RouterMiddleware($c->get('system.router'));
    },
    'system.middleware.response_sender' => function($c) {
        return new ResponseSenderMiddleware();
    }
];
