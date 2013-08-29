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
    private static $environment = 1000;

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
     * Sets up the environment
     *
     * @return null
     */
    public static function initialize() {
        error_reporting(-1);
        set_error_handler(array('\Maverick\Lib\ErrorHandler', 'handleError'));

        set_exception_handler(array('\Maverick\Lib\ErrorHandler', 'handleException'));

        if(\Maverick\Maverick::getConfig('Environment')->get('display_errors')) {
            ini_set('display_errors', 0);
        }

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
            $values = array_flip($this->environments);

            if(array_key_exists($env, $values)) {
                $this->environment = $code = $env;
            }
        } else {
            if(array_key_exists($env, $this->environments)) {
                $this->environment = $code = $this->environments[$env];
            }
        }

        if(!$code) {
            throw new \Exception('Invalid environment ' . $env);
        }

        return $code;
    }

    /**
     * Gets and returns the environment
     *
     * @param  boolean $getCode=false
     * @return string | integer
     */
    public static function getEnvironment($getCode=false) {
        if(!self::$environment) {
            $load = ROOT_PATH . 'ENVIRONMENT';
    
            if(file_exists($load)) {
                $env = strtoupper(file_get_contents($load));
    
                if(array_key_exists($env, $this->environments)) {
                    self::setEnvironment($env);
                }
            }
        }

        if($getCode) {
            return $this->environment;
        }

        $values = array_flip(self::$environments);
        return $values[self::$environment];
    }

    /**
     * Determines if an environment is below the current one
     *
     * @throws \Exception
     * @param  string $check
     * @return null
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
}