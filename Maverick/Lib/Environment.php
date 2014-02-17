<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Environment {
    /**
     * The current environment
     *
     * @var string $environment
     */
    private static $environment = null;

    /**
     * A list of possible environments in order
     * with their individual codes
     *
     * @var array $environments
     */
    private static $environments = array('DEV'  => 1000,
                                         'TEST' => 1500,
                                         'BETA' => 2000,
                                         'PROD' => 2500);

    /**
     * Sets up the environment, including:
     *  - The error handler
     *  - 
     */
    public static function initialize() {
        self::$environments = \Maverick\Maverick::getConfig('Environments')->getAsArray() ?: self::$environments;
        self::$environment  = array_values(self::$environments)[0];

        error_reporting(\Maverick\Maverick::getConfig('Environment')->get('report_errors'));
        set_error_handler(array('\Maverick\Lib\ErrorHandler', 'handleError'));
        set_exception_handler(array('\Maverick\Lib\ErrorHandler', 'handleException'));
        register_shutdown_function(array('\Maverick\Lib\Environment', 'shutdown'));

        ini_set('display_errors', 0);

        if(\Maverick\Maverick::getConfig('Environment')->get('log_errors')) {
            ini_set('log_errors', 'TRUE');
            ini_set('error_log', \Maverick\Maverick::getConfig('environment')->get('error_log_file'));
        }
    }

    /**
     * Set the environment
     *
     * @throws \Exception
     * @param  string | integer $env
     * @return integer
     */
    private static function setEnvironment($env) {
        $code = 0;

        if(is_numeric($env)) {
            $values = array_flip(self::$environments);

            if(array_key_exists($env, $values)) {
                $code = $env;
            }
        } else {
            if(array_key_exists($env, self::$environments)) {
                $code = self::$environments[$env];
            }
        }

        if(!$code) {
            throw new \Exception('Invalid environment ' . $env);
        }

        self::$environment = $code;

        return $code;
    }

    /**
     * Gets and returns the environment
     *
     * @param  boolean $getCode=false
     * @return string | integer
     */
    public static function getEnvironment($getCode=false) {
        if(is_null(self::$environment)) {
            $load = ROOT_PATH . 'ENVIRONMENT';

            if(file_exists($load)) {
                $env = strtoupper(trim(file_get_contents($load)));

                self::setEnvironment($env);
            }
        }

        if($getCode) {
            return self::$environment;
        }

        $values = array_flip(self::$environments);
        return $values[self::$environment];
    }

    /**
     * Determines if an environment is below the current one
     *
     * @throws \Exception
     * @param  string $check
     */
    public static function lessThan($check) {
        if(array_key_exists($check, self::$environments)) {
            $code = self::$environments[$check];

            if(self::$environment < $code) {
                return true;
            } else {
                return false;
            }
        } else {
            throw new \Exception('Invalid environment ' . $check);
        }
    }

    /**
     * The shutdown function for Maverick
     */
    public static function shutdown() {
        $error = error_get_last();

        if(is_array($error)) {
            call_user_func_array(array('\Maverick\Lib\ErrorHandler', 'handleError'), $error);
        }
    }
}