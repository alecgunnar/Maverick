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
     * Redirects the user
     *
     * @param $url
     * @param $msg=''
     */
    public static function location($url, $msg='') {
        if($msg && isset($_SESSION)) {
            $_SESSION['http_redirect_message'] = $msg;
        }

        header('Location: ' . $url);

        exit;
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