<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Http;

use Maverick\DataStructure\UserInputMap,
    Maverick\DataStructure\ReadOnlyMap,
    Maverick\Http\Session\Cookie;

class Session {
    /**
     * The cookies sent with this request
     *
     * @var Maverick\DataStructure\ReadOnlyMap
     */
    protected $cookies;

    /**
     * Constructor
     */
    public function __construct() {
        $cleanedCookies = new UserInputMap($_COOKIE);
        $cookies        = [];

        foreach($cleanedCookies as $name => $value) {
            $cookies[$name] = new Cookie($name, $value);
        }

        $this->cookies = new ReadOnlyMap($cookies);
    }

    /**
     * Gets all of the cookies
     *
     * @codeCoverageIgnore
     * @return Maverick\DataStructure\ReadOnlyMap
     */
    public function getCookies() {
        return $this->cookies;
    }
}