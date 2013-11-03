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
     * The URI for the current page
     *
     * @var string $uri
     */
    private static $uri = '';

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

        $appRoot = new \Application\Controller\AppRoot;
        $appRoot->preload();

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

        $appRoot->postload();
    }
    
    /**
     * Routes the request automatically
     *
     * @return array
     */
    private static function routeAutomatically() {
        $pathToController = APPLICATION_PATH . 'Controller' . DS;
        $expUri           = $params = explode('/', self::getUri());
        $namespace        = '';
        $controller       = '';

        foreach($expUri as $u) {
            $uri   = self::convertUri($u);
            $shift = false;

            if(file_exists($pathToController . $uri . PHP_EXT)) {
                $controller = $namespace . $uri;
                $shift      = true;
            }
            
            if(is_dir($pathToController . $uri)) {
                $namespace        .= $uri . '_';
                $pathToController .= $uri . DS;
                $shift             = true;
            }

            if($shift) {
                array_shift($params);
            }
        }

        if(count($expUri) == count($params)) {
            $controller = 'Errors_404';
        }

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

        $uri = self::getUri();

        foreach($routes as $match => $cntrlr) {
            if(preg_match('~^(?:' . trim($match, '/') . ')$~', $uri, $params)) {
                $controller = $cntrlr;
            }
        }

        if(!$controller) {
            if(\Maverick\Maverick::getConfig('System')->get('default_auto_route')) {
                return self::routeAutomatically();
            }

            $controller = 'Errors_404';
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
        $controllerClassWithNamespace = 'Application\Controller\\' . $controller;
        $inst                         = new $controllerClassWithNamespace;

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

        self::$uri = trim(str_replace('index.php', '', $uri), '/');
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
}