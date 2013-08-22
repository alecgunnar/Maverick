<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick;

class Maverick {
    /**
     * The singleton instance
     *
     * @var \Maverick\Maverick | null $instance
     */
    private static $instance = null;

    /**
     * Records whether or not the website has been "launched"
     *
     * @var boolean
     */
    private $launched = false;

    /**
     * Just the constructor
     *
     * @return null
     */
    protected function __construct() { }

    /**
     * Gets and returns the singleton instance
     *
     * @return \Maverick\Environment
     */
    public static function getInstance() {
        if(is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Gets the application rolling
     *
     * @param  boolean $registerAutoloader=true
     * @param  boolean $setErrorHandler=true
     * @param  boolean $routePage=true
     * @return null
     */
    public function Launch($registerAutoloader=true, $setErrorHandler=true) {
        if($this->launched) {
            return false;
        }

        $this->launched = true;

        if($registerAutoloader) {
            $this->registerAutoloader();
        }

        \Maverick\Lib\Environment::getInstance();

        if($setErrorHandler) {
            $this->setErrorHandler();
        }

        \Maverick\Lib\Output::getInstance()->getTplEngine();

        $this->routePage();

        \Maverick\Lib\Router::getInstance()->getController()
            ->printOut();
    }

    /**
     * Registers the autoloader
     *
     * @return null
     */
    public function registerAutoloader() {
        require_once(MAVERICK_PATH . 'Lib' . DS . 'Autoloader.php');
        spl_autoload_register('Maverick\Lib\Autoloader::autoload');
    }

    /**
     * Sets the error handler
     *
     * @return null
     */
    public function setErrorHandler() {
        error_reporting(E_ALL);
        ini_set('log_errors', 'TRUE');
        ini_set('error_log', MAVERICK_PATH . 'Logs' . DS . 'ErrorLogs_' . date('n-j-Y') . '.txt');
        set_error_handler('\Maverick\Lib\ErrorHandler::HandelError');
    }

    /**
     * Routes the page
     *
     * @return null
     */
    public function routePage() {
        \Maverick\Lib\Router::getInstance()->route();
    }

    /**
     * Gets a config file
     *
     * @param  string $configName
     * @return array | null
     */
    public static function getConfig($configName) {
        $config = array();
        $key    = strtolower($configName);
        $path   = ROOT_PATH . 'Config/%s/' . ucfirst($configName) . PHP_EXT;
        $lookIn = \Maverick\Lib\Environment::getInstance()->getEnvironment();

        $master = sprintf($path, 'Master');

        if(file_exists($master)) {
            $config = include($master);
        }

        $env        = \Maverick\Lib\Environment::getInstance()->getEnvironment();
        $currentEnv = sprintf($path, $env);

        if(file_exists($currentEnv)) {
            $config = array_merge($config, include($currentEnv));
        }

        return new \Maverick\Lib\Model($config);
    }
}