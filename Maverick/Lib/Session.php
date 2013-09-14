<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Session {
    /**
     * Holds the model for the cookies
     *
     * @var \Maverick\Model_Input | null $cookies
     */
    private static $cookies = null;

    /**
     * The user model -- this model should represent the current user
     * This can be application specific
     *
     * @var mixed $userModel
     */
    private static $userModel = null;

    /**
     * This says whether or not the current user is logged in
     *
     * @var boolean $userStatus
     */
    private static $userStatus = false;

    /**
     * Starts the session
     *
     * @return null
     */
    public static function initialize() {
        self::$cookies = new Model_Input($_COOKIE);
    }

    /**
     * Sets a cookie for the session
     *
     * @param  string $name
     * @param  string $value
     * @param  integer | null $expire
     * @return null
     */
    public static function setCookie($name, $value, $expire=null) {
        setcookie($name, $value, $expire);

        if(is_null($expire) || $expire < time()) {
            self::$cookies->set($name, $value);
        }
    }

    /**
     * Gets the cookies for the session
     *
     * @return \Maverick\Model_input
     */
    public static function getCookies() {
        return self::$cookies;
    }

    /**
     * Tells the session that the user is logged in
     *
     * @return null
     */
    public static function userLoggedIn() {
        self::$userStatus = true;
    }

    /**
     * Gets the user's status
     *
     * @return boolean
     */
    public static function getUserStatus() {
        return self::$userStatus;
    }

    /**
     * Gives the session the user model
     *
     * @param  mixed $model
     * @return null
     */
    public static function setUserModel($model) {
        self::$userModel = $model;
    }

    /**
     * Returns the user model to the controller
     *
     * @return mixed
     */
    public static function getUserModel() {
        return self::$userModel;
    }
}