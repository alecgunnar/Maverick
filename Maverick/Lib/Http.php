<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Http {
    /**
     * The current request headers
     *
     * @var $headers
     */
    private static $headers = array();

    /**
     * The redirect message for the current request
     *
     * @var string|null
     */
    private static $redirectMessage = null;

    /**
     * Redirects the user
     *
     * @param $url
     * @param $msg=''
     */
    public static function location($url, $msg='') {
        if($msg && session_id()) {
            $_SESSION['http_redirect_message'] = $msg;
        }

        header('Location: ' . $url);

        exit;
    }

    /**
     * Gets the redirect message
     *
     * @return string
     */
    public static function getRedirectMessage() {
        if(is_null(self::$redirectMessage)) {
            if(isset($_SESSION) && array_key_exists('http_redirect_message', $_SESSION)) {
                self::$redirectMessage = $_SESSION['http_redirect_message'];

                unset($_SESSION['http_redirect_message']);
            } else {
                self::$redirectMessage = '';
            }
        }

        return self::$redirectMessage;
    }

    /**
     * Fetches all of the http headers
     *
     * @return array | boolean
     */
    public static function getHeaders() {
        if(count(self::$headers) === 0) {
            self::$headers = getallheaders();
        }

        return self::$headers;
    }
}