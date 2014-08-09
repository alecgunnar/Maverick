<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick;

use Maverick\Http\Request,
    Maverick\Http\Router,
    Maverick\Http\Response,
    Maverick\Http\Session,
    Maverick\Controller\ExceptionController,
    Maverick\DependencyManagement\ServiceManager,
    Maverick\Exception\NoRouteException,
    Exception;

class Application {
    /**
     * The current version
     *
     * @var string
     */
    const VERSION = '0.1.0';

    /** 
     * The current request being worked with
     *
     * @var Maverick\Http\Request
     */
    public $request;

    /**
     * The router for the current request
     *
     * @var Maverick\Http\Router
     */
    public $router;

    /**
     * The response for the current request
     *
     * @var Maverick\Http\Response
     */
    public $response;

    /**
     * The session for the current request
     *
     * @var Maverick\Http\Session
     */
    public $session;

    /**
     * The service manager to be used by the app
     *
     * @var Maverick\DependencyManagement\ServiceManager
     */
    public $services;

    /**
     * Constructor
     */
    public function __construct() {
        $this->registerErrorHandler();

        $this->services = new ServiceManager();

        $this->registerDefaultServices();

        $this->request  = $this->services->get('request');
        $this->response = $this->services->get('response');
        $this->router   = $this->services->get('router');
        $this->session  = $this->services->get('session');
    }

    /**
     * Registers default services with the container
     *
     * @codeCoverageIgnore
     */
    private function registerDefaultServices() {
        $this->services->register('request', function() {
            return new Request();
        });

        $this->services->register('response', function($mgr) {
            return new Response($mgr->get('request'));
        });

        $this->services->register('router', function($mgr) {
            return new Router($mgr->get('request'), $mgr->get('response'));
        });

        $this->services->register('session', function() {
            return new Session();
        });

        $this->services->register('exception.controller', function($mgr) {
            return new ExceptionController($mgr->get('response'));
        });
    }

    /**
     * Registers the error handler
     *
     * @codeCoverageIgnore
     */
    private function registerErrorHandler() {
        ini_set('error_reporting', 'E_ALL');
        ini_set('display_errors', 0);

        $shutdown = function(Exception $exception) {
            $this->response->setBody($this->services->call('exception.controller->showErrorAction', [$exception]));
            $this->response->send();
        };

        $errorHandler = function($num, $str, $file, $line) use($shutdown) {
            $shutdown(new Exception($str . ' in ' . $file . ' on line ' . $line . '.'));
        };

        set_exception_handler($shutdown);
        set_error_handler($errorHandler);

        register_shutdown_function(function() use($errorHandler) {
            if($err = error_get_last()) {
                call_user_func_array($errorHandler, $err);
            }
        });
    }

    /**
     * Finishes off the request, and sends the response
     *
     * @throws Maverick\Exception\NoRouteException
     */
    public function finish() {
        if(!$this->router->hasRouted()) {
            throw new NoRouteException('No route exists for ' . htmlentities($this->request->getUrn()) . ' using method ' . htmlentities($this->request->getMethod()) . ' and ' . ($this->request->isHttps() ? 'https' : 'http'));
        }

        $this->response->send();
    }
}