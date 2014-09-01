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
    Maverick\Controller\ErrorController,
    Maverick\DependencyManagement\ServiceManager,
    Maverick\Exception\InvalidValueException,
    Maverick\Exception\NoRouteException,
    Exception,
    Maverick\DataStructure\ReadOnlyMap,
    Maverick\Http\Response\Instruction\ErrorInstruction;

class Application {
    /**
     * The current version
     *
     * @var string
     */
    const VERSION = '0.4.1';

    /**
     * Debug level for the app
     *
     * @var int
     */
    protected static $debugLevel;

    /**
     * Various debug levels
     *
     * @var int
     */
    const DEBUG_LEVEL_DEV  = 1005;
    const DEBUG_LEVEL_TEST = 1010;
    const DEBUG_LEVEL_BETA = 1015;
    const DEBUG_LEVEL_PROD = 1020;

    /**
     * Environments
     *
     * @var array
     */
    protected static $levels = [
        1005 => 'dev',
        1010 => 'test',
        1015 => 'beta',
        1020 => 'prod'
    ];

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
     *
     * @throws Exception
     */
    public function __construct() {
        // @codeCoverageIgnoreStart
        if(!defined('ROOT')) {
            throw new Exception('You must define "ROOT" before creating a new application!');
        }

        if(!self::$debugLevel) {
            self::$debugLevel = self::DEBUG_LEVEL_PROD;
        }
        // @codeCoverageIgnoreEnd

        $this->registerErrorHandler();

        $this->services = new ServiceManager();

        $this->registerDefaultServices();
    }

    /**
     * Assigns the primary services to the attributes of this class
     */
    public function start() {
        $this->request  = $this->services->get('request');
        $this->response = $this->services->get('response');
        $this->router   = $this->services->get('router');
        $this->session  = $this->services->get('session');
    }

    /**
     * Sets the debug level
     *
     * @var int $level
     */
    public static function setDebugLevel($level) {
        if(!isset(self::$levels[$level])) {
            throw new InvalidValueException('Debug level ' . $level . ' is not valid.');
        }

        self::$debugLevel = $level;
    }

    /**
     * Gets the debug level
     *
     * @codeCoverageIgnore
     * @return int
     */
    public static function getDebugLevel() {
        return self::$debugLevel;
    }

    /**
     * Compares the debug level to another
     *
     * All comparisions are handled in this way:
     *
     * {current level} {method} {compare level}
     *
     * @param  string $method
     * @param  int    $comareTo
     * @return boolean
     */
    public static function debugCompare($method, $compareTo) {
        switch($method) {
            case '>':
                return self::$debugLevel > $compareTo;
            case '>=':
                return self::$debugLevel >= $compareTo;
            case '<':
                return self::$debugLevel < $compareTo;
            case '<=':
                return self::$debugLevel <= $compareTo;
            case '==':
            case '===':
                return self::$debugLevel === $compareTo;
            case '!=':
                return self::$debugLevel != $compareTo;
            default:
                throw new InvalidValueException($method . ' is not a valid compare method. Please try: >, >=, <, <=, == or !=.');
        }

        return false;
    }

    /**
     * Loads configuration information
     *
     * @param  string $name
     * @return Maverick\DataStructure\ReadOnlyMap
     */
    public static function getConfig($name) {
        $dir    = ROOT . 'config/';
        $env    = $dir . self::$levels[self::$debugLevel] . '/';
        $master = $dir . 'master/';

        $config = [];

        if(file_exists($master . $name . '.php')) {
            $config = include $master . $name . '.php';
        }

        if(file_exists($env . $name . '.php')) {
            $config = array_merge($config, include $env . $name . '.php');
        }

        return new ReadOnlyMap($config);
    }

    /**
     * Registers default services with the container
     *
     * @codeCoverageIgnore
     */
    private function registerDefaultServices() {
        $app = $this;

        $this->services->register('request', function() {
            return new Request();
        });

        $this->services->register('response', function($mgr) {
            return new Response($mgr->get('request'), $mgr->get('session'));
        });

        $this->services->register('router', function($mgr) {
            return new Router($mgr->get('request'), $mgr->get('response'), $mgr);
        });

        $this->services->register('session', function() {
            return new Session();
        });

        $this->services->register('error.controller', function() {
            return new ErrorController();
        });
    }

    /**
     * Registers the error handler
     *
     * @codeCoverageIgnore
     */
    private function registerErrorHandler() {
        if(self::$debugLevel === self::DEBUG_LEVEL_TEST) {
            ini_set('display_errors', '1');
            return;
        }

        ini_set('display_errors', '0');

        $shutdown = function(Exception $exception) {
            $code = 500;

            if(get_class($exception) == 'Maverick\Exception\NoRouteException') {
                $code = 404;
            }

            $controller = $this->services->get('error.controller')->setException($exception);

            ErrorInstruction::factory($code)->instruct($this->services->get('response'), $controller);
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
     * @codeCoverageIgnore
     * @throws Maverick\Exception\NoRouteException
     */
    public function finish() {
        if(!$this->router->doRoute()) {
            throw new NoRouteException('No route exists for ' . htmlentities($this->request->getUrn()) . ' using method ' . htmlentities($this->request->getMethod()) . ' and ' . ($this->request->isHttps() ? 'https' : 'http'));
        }

        $this->response->send();
    }
}