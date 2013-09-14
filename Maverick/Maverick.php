<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick;

class Maverick {
    /**
     * Records whether or not the website has been "launched"
     *
     * @var boolean
     */
    private static $launched = false;

    /**
     * Gets the application rolling
     *
     * @param  boolean $registerAutoloader=true
     * @return null
     */
    public static function Launch($registerAutoloader=true) {
        if(self::$launched) {
            return false;
        }

        self::$launched = true;

        if($registerAutoloader) {
            self::registerAutoloader();
        }

        \Maverick\Lib\Environment::initialize();
        \Maverick\Lib\Session::initialize();
        \Maverick\Lib\Output::initialize();

        \Maverick\Lib\Router::route();
        \Maverick\Lib\Router::getController()
            ->printOut();
    }

    /**
     * Registers the autoloader
     *
     * @return null
     */
    private static function registerAutoloader() {
        require_once(MAVERICK_PATH . 'Lib' . DS . 'Autoloader.php');
        spl_autoload_register('Maverick\Lib\Autoloader::autoload');
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
        $path   = ROOT_PATH . 'Config' . DS . '%s' . DS . ucfirst($configName) . PHP_EXT;

        $master = sprintf($path, 'Master');

        if(file_exists($master)) {
            $config = include($master);
        }

        $env        = ucfirst(strtolower(\Maverick\Lib\Environment::getEnvironment()));
        $currentEnv = sprintf($path, $env);

        if(file_exists($currentEnv)) {
            $config = array_merge($config, include($currentEnv));
        }

        return new \Maverick\Lib\Model($config);
    }
}