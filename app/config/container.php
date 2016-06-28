<?php

use Maverick\Middleware\RouterMiddleware;
use Maverick\Router\FastRouteRouter;
use Maverick\Router\Collection\RouteCollection;
use Maverick\Router\Collection\Decorator\FastRouteRouteCollectionDecorator;
use Maverick\Router\Loader\CallbackRouteLoader;
use Maverick\Router\Loader\RouteLoader;
use Maverick\Router\Collection\Factory\RouteCollectionFactory;
use Maverick\Router\Entity\Factory\RouteEntityFactory;
use Maverick\Controller\NotFoundController;
use Maverick\Controller\NotAllowedController;
use Maverick\ErrorHandler\WhoopsErrorHandler;
use Maverick\Utility\UriBuilder\FastRouteUriBuilder;
use Maverick\Resolver\HandlerResolver;
use Relay\Middleware\ResponseSender as ResponseSenderMiddleware;
use Whoops\Run as WhoopsRunner;
use Whoops\Handler\PrettyPageHandler;
use FastRoute\RouteParser\Std as FastRouteParser;

return [
    /*
     * System - essential services
     */
    'system.route_collection' => function($c) {
        $collection = new RouteCollection();

        $c->get('system.route_loader')
            ->loadRoutes($collection);

        return $collection;
    },
    'system.route_loader' => function($c) {
        return new CallbackRouteLoader(
            $c->get('system.config.routes'),
            $c->get('system.router.collection.factory'),
            $c->get('system.router.entity.factory')
        );
    },
    'system.router' => function($c) {
        return new FastRouteRouter($c->get('fast_route.dispatcher'));
    },
    'system.router.collection.factory' => function() {
        return new RouteCollectionFactory();
    },
    'system.router.entity.factory' => function() {
        return new RouteEntityFactory();
    },
    'system.config.routes' => function($c) {
        return require($c->get('system.config.routes_file'));
    },
    'system.config.routes_file' => __DIR__ . '/router.php',
    'system.controller.not_found' => function() {
        return new NotFoundController();
    },
    'system.controller.not_allowed' => function() {
        return new NotAllowedController();
    },
    'system.middleware.router' => function($c) {
        return new RouterMiddleware(
            $c->get('system.router'),
            $c->get('system.controller.not_found'),
            $c->get('system.controller.not_allowed'),
            $c->get('system.resolver')
        );
    },
    'system.middleware.response_sender' => function($c) {
        return new ResponseSenderMiddleware();
    },
    'system.error_handler' => function($c) {
        return new WhoopsErrorHandler(
            $c->get('whoops.runner'),
            $c->get('whoops.handler')
        );
    },
    'system.resolver' => function($c) {
        return new HandlerResolver($c);
    },

    /*
     * Utilities - stuff that's helpful
     */
    'utility.uri_builder' => function($c) {
        return new FastRouteUriBuilder(
            $c->get('fast_route.parser'),
            $c->get('system.route_collection')
        );
    },

    /*
     * Fast Route
     */
    'fast_route.definitions' => function($c) {
        return new FastRouteRouteCollectionDecorator($c->get('system.route_collection'));
    },
    'fast_route.dispatcher' => function($c) {
        return \FastRoute\simpleDispatcher(
            $c->get('fast_route.definitions'),
            $c->get('fast_route.options')
        );
    },
    'fast_route.parser' => function() {
        return new FastRouteParser();
    },
    'fast_route.options' => function() {
        return [];
    },

    /*
     * Whoops!
     */
    'whoops.runner' => function() {
        return new WhoopsRunner();
    },
    'whoops.handler' => function() {
        return new PrettyPageHandler();
    }
];
