<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Environment {
    /**
     * The singleton instance
     *
     * @var \Maverick\Environment | null $instance
     */
    private static $instance = null;

    /**
     * A list of possible environments in order
     * with their individual codes
     *
     * @var array $environments
     */
    public $environments = array('DEV'  => 1000,
                                 'TEST' => 1500,
                                 'BETA' => 2000,
                                 'PROD' => 2500);

    /**
     * The current environment
     *
     * @var string $environment
     */
    public $environment = 1000;

    /**
     * Sets up the environment
     *
     * @return null
     */
    protected function __construct() {
        $load = ROOT_PATH . 'ENVIRONMENT';

        if(file_exists($load)) {
            $env = strtoupper(file_get_contents($load));

            if(array_key_exists($env, $this->environments)) {
                $this->setEnvironment($env);
            }
        }
    }

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
     * Set the environment
     *
     * @throws \Exception
     * @param  string | integer $env
     * @return integer
     */
    public function setEnvironment($env) {
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
    public function getEnvironment($getCode=false) {
        if($getCode) {
            return $this->environment;
        }

        $values = array_flip($this->environments);
        return $values[$this->environment];
    }

    /**
     * Determines if an environment is below the current one
     *
     * @throws \Exception
     * @param  string $check
     * @return null
     */
    public function lessThan($check) {
        if(array_key_exists($check, $this->environments)) {
            $code = $this->environments[$check];

            if($this->environment < $code) {
                return true;
            } else {
                return false;
            }
        } else {
            throw new \Exception('Invalid environment ' . $check);
        }
    }
}