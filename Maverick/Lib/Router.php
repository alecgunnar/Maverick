<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Router {
    /**
     * Has the router...... routed?
     *
     * @var boolean $routed=false
     */
    private static $routed = false;

    /**
     *  Whether or not automatic routing was used
     *
     * @var boolean
     */
    private static $autoRouted = false;

    /**
     * The URI for the current page
     *
     * @var \Maverick\Lib\Model_Uri $uri
     */
    private static $uri = null;

    /**
     * The URN
     *
     * @var string $urn
     */
    private static $urn = '';

    /**
     * A controller which will be forced to load
     *
     * @var string $forceLoad
     */
    private static $forceLoad = null;

    /**
     * The controller the page has been routed to
     *
     * @var mixed $controllerObject
     */
    private static $controllerObject = null;

    /**
     * The name of the controller's class
     *
     * @var string $controllerClass
     */
    private static $controllerClass = '';

    /**
     * The root controller
     *
     * @var \Application\Controller\AppRoot | null
     */
    private static $appRoot = null;

    /**
     * Set a controller to be forcibly loaded
     *
     * @param string $controller
     */
    public static function forceLoadController($controller) {
        self::$forceLoad = $controller;
    }

    /**
     * Do the routing
     */
    public static function route() {
        if(self::$routed) {
            return false;
        }

        self::$routed = true;

        self::$appRoot = new \Application\Controller\AppRoot;
        self::$appRoot->preload();

        $controller = '';
        $params     = array();

        if(self::$forceLoad) {
            $controller = self::$forceLoad;
        } else {
            $defaultController = 'Index';
    
            if(\Maverick\Maverick::getConfig('System')->get('auto_route')) {
                if(self::getUri()) {
                    list($controller, $params) = self::routeAutomatically();
                }
            } else {
                list($controller, $params) = self::routeDefined();
            }
    
            if(!$controller) {
                $controller = $defaultController;
            }
        }

        self::loadController($controller, $params);
    }
    
    /**
     * Routes the request automatically
     *
     * @return array
     */
    private static function routeAutomatically() {
        self::$autoRouted  = true;

        $pathToController  = APPLICATION_PATH . 'Controller' . DS;
        $expUri            = $params = explode('/', self::$uri->getPath());
        $lastWasController = false;
        $namespace         = '';
        $controller        = '';

        foreach($expUri as $u) {
            $uri   = self::convertUri($u);
            $shift = false;

            if(file_exists($pathToController . $uri . PHP_EXT) && !$lastWasController) {
                $controller        = $namespace . $uri;
                $shift             = true;
                $lastWasController = true;
            }
            
            if(is_dir($pathToController . $uri) && (!$controller || ($namespace . $uri) == $controller)) {
                $namespace        .= $uri . '_';
                $pathToController .= $uri . DS;
                $shift             = true;
                $lastWasController = false;
            }

            if($shift) {
                array_shift($params);
            } else {
                break;
            }
        }

        if(count($expUri) == count($params)) {
            self::throw404();
        }
dump($params, $namespace, $pathToController, $controller);
        return array($controller, $params);
    }
    
    /**
     * Routes the request if it is defined
     *
     * @throws \Exception
     * @return array
     */
    private static function routeDefined() {
        $routes     = \Maverick\Maverick::getConfig('Routes')->getAsArray();
        $controller = '';
        $params     = array();

        if(!count($routes)) {
            throw new \Exception('You have not defined any routes.');
        }

        foreach($routes as $match => $cntrlr) {
            if(preg_match('~^(?:' . trim($match, '/') . ')$~', self::$uri->getResourcePath(), $params)) {
                $controller = $cntrlr;
            }
        }

        if(!$controller) {
            if(\Maverick\Maverick::getConfig('System')->get('default_auto_route')) {
                return self::routeAutomatically();
            }

            self::throw404();
        }

        array_shift($params);

        return array($controller, $params);
    }
    
    /**
     * Converts a string from the URI to the file naming standard
     *
     * @param  string $name
     * @return string
     */
    public static function convertUri($name) {
        return implode('', array_map(function($a) {
            return ucfirst($a); 
        }, explode('-', $name)));
    }

    /**
     * Loads a specific controller
     *
     * @param  string $controller
     * @param  array  $variables=array()
     * @return mixed
     */
    public static function loadController($controller, $variables=array()) {
        $controllersNamespace         = 'Application\Controller\\';
        $controllerClassWithNamespace = $controllersNamespace . $controller;
        $inst                         = new $controllerClassWithNamespace;

        if(self::$autoRouted) {
            $splitSubNamespace = explode('_', $controller);
            $rootController    = $splitSubNamespace[0];

            if(method_exists($controllersNamespace . $rootController, 'rootSetup')) {
                $rootControllerWithNamespace = $controllersNamespace . $rootController;
                $rootControllerWithNamespace::rootSetup();
            }
        }

        self::$controllerObject = $inst;
        self::$controllerClass  = $controller;

        call_user_func_array(array($inst, 'main'), $variables);

        return $inst;
    }

    /**
     * Determines the URI for the router
     *
     * @return string
     */
    private static function setUri() {
        if(!array_key_exists('ORIG_PATH_INFO', $_SERVER)) {
            $uri      = explode('?', trim($_SERVER['REQUEST_URI'], '/'));
            $path     = $uri[0];
            $script   = trim($_SERVER['SCRIPT_NAME'], '/');

            $expUri    = explode('/', $path);
            $expScript = explode('/', $script);

            if(count($expScript) > 1) {
                foreach($expScript as $n => $f) {
                    if(array_key_exists($n, $expUri)) {
                        if($expUri[$n] == $f) {
                            unset($expUri[$n]);
                        } else break;
                    }
                }
            }
    
            $uri = implode($expUri, '/');
        } else {
            $uri = $_SERVER['ORIG_PATH_INFO'];
        }

        self::$uri = Builder_Uri::deconstructUri((array_key_exists('HTTPS', $_SERVER) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    }

    /**
     * Gets the URI
     *
     * @return string
     */
    public static function getUri() {
        if(!self::$uri) {
            self::setUri();
        }

        return self::$uri;
    }

    /**
     * Get the controller which was routed to
     *
     * @param  boolean $getName=false
     * @return mixed
     */
    public static function getController($getName=false) {
        if($getName) {
            return self::$controllerClass;
        }

        return self::$controllerObject;
    }

    /**
     * Calls the post load method of the controller
     */
    public static function doPostLoad() {
        self::$appRoot->postLoad();
    }

    /**
     * Shows the "404 - Page not Found" error page
     */
    public static function throw404() {
        Http::setResponseCode(404);

        self::loadController('Errors_404');

        self::getController()->printOut();

        exit;
    }
}