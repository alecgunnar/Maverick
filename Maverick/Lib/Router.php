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
     * Do the routing
     *
     * @return null
     */
    public static function route() {
        if(self::$routed) {
            return false;
        }

        self::$routed = true;

        $appRoot = new \Application\Controller\AppRoot;
        $appRoot->main();

        $defaultController = 'Index';

        self::setUri();

        $uri        = self::getUri();
        $pathTo     = ROOT_PATH . 'Application/Controller/';
        $controller = '';
        $params     = array();

        if($uri) {
            $expUri = $params = explode('/', $uri);

            foreach($expUri as $uri) {
                $i = implode('', array_map(function($a) {
                   return ucfirst($a); 
                }, explode('-', $uri)));

                if(is_dir($pathTo . $i) || file_exists($pathTo . $i . PHP_EXT)) {
                    $pathTo .= $i . '/';
    
                    if($controller) $controller .= '_';
                    $controller                 .= $i;
    
                    array_shift($params);
                } else break;
            }

            if(count($expUri) == count($params)) {
                $controller = 'Errors_404';
            }
        } else {
            $controller = $defaultController;
            $expUri     = array();
        }

        self::loadController($controller, $params);
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
                    if($expUri[$n] == $f) {
                        unset($expUri[$n]);
                    } else break;
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