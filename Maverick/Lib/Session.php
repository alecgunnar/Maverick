<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Session {
    /**
     * The instance of Session
     *
     * @var \Maverick\Session | null $instance
     */
    private static $instance = null;

    /**
     * Holds the model for the cookies
     *
     * @var \Maverick\Model_Input | null $cookies
     */
    //private $cookies = null;

    /**
     * The user model -- this model should represent the current user
     * This can be application specific
     *
     * @var mixed $userModel
     */
    //private $userModel = null;

    /**
     * This says whether or not the current user is logged in
     *
     * @var boolean $userStatus
     */
    private $userStatus = false;

    /**
     * The Model for this session
     *
     * private \Maverick\Lib\Model_Session | null $model
     */
    private $model = null;

    /**
     * Starts the session
     *
     * @return null
     */
    private function __construct() {
        $cookies = new Model_Input($_COOKIE);

        $this->model = new Model_Session(array('cookies' => $cookies));
    }

    /**
     * Gets the instance of session
     *
     * @return \Maverick\Session
     */
    public static function getInstance() {
        if(is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Sets a cookie for the session
     *
     * @param  string $name
     * @param  string $value
     * @param  integer | null $expire
     * @return null
     */
    public function setCookie($name, $value, $expire=null) {
        setcookie($name, $value, $expire);

        if(is_null($expire) || $expire < time()) {
            $this->getCookies()->set($name, $value);
        }
    }

    /**
     * Gets the cookies for the session
     *
     * @return \Maverick\Model_input
     */
    public function getCookies() {
        return $this->model->get('cookies');
    }

    /**
     * Tells the session that the user is logged in
     *
     * @return null
     */
    public function userLoggedIn() {
        $this->userStatus = true;
    }

    /**
     * Gets the user's status
     *
     * @return boolean
     */
    public function getUserStatus() {
        return $this->userStatis;
    }

    /**
     * Gives the session the user model
     *
     * @param  mixed $model
     * @return null
     */
    public function setUserModel($model) {
        $this->model->set('user', $model);
    }

    /**
     * Returns the user model to the controller
     *
     * @return mixed (the type of $this->userModel)
     */
    public function getUserModel() {
        return $this->model->get('user');
    }
}