<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Router {
    /**
     * The singleton instance of this class
     *
     * @var \Maverick\Lib\Router | null
     */
    private static $instance = null;

    /**
     * Has the router...... routed?
     *
     * @var boolean $routed=false
     */
    private $routed = false;

    /**
     * The URI for the current page
     *
     * @var string $uri
     */
    private $uri = '';

    /**
     * The controller the page has been routed to
     *
     * @var mixed $controllerObject
     */
    private $controllerObject = null;

    /**
     * The name of the controller's class
     *
     * @var string $controllerClass
     */
    private $controllerClass = '';

    /**
     * The constructor
     *
     * @return null
     */
    private function __construct() { }

    /**
     * Returns the singleton instance of this class
     *
     * @return \Maverick\Lib\Router
     */
    public static function getInstance() {
        if(is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Do the routing
     *
     * @return null
     */
    public function route() {
        if($this->routed) {
            return false;
        }

        $this->routed = true;

        $defaultController = 'Index';

        $this->setUri();

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

        $appRoot = new \Application\Controller\AppRoot;
        $appRoot->main();

        $controllerClassWithNamespace = 'Application\Controller\\' . $controller;
        $inst                         = new $controllerClassWithNamespace;

        $this->controllerObject = $inst;
        $this->controllerClass  = $controller;

        call_user_func_array(array($inst, 'main'), $params);
    }

    /**
     * Determines the URI for the router
     *
     * @return string
     */
    private function setUri() {
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

        $this->uri = trim(str_replace('index.php', '', $uri), '/');
    }

    /**
     * Gets the URI
     *
     * @return string
     */
    public function getUri() {
        return $this->uri;
    }

    /**
     * Get the controller which was routed to
     *
     * @param  boolean $getName=false
     * @return mixed
     */
    public function getController($getName=false) {
        if($getName) {
            return $this->controllerClass;
        }

        return $this->controllerObject;
    }
}