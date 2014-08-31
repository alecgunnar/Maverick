<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Http;

use Maverick\DataStructure\Map,
    Maverick\DataStructure\UserInputMap,
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
     * The cookies which will be set
     *
     * @var Maverick\DataStructure\ArrayList
     */
    protected $newCookies;

    /**
     * Constructor
     */
    public function __construct() {
        $cleanedCookies = new UserInputMap($_COOKIE);
        $cookies        = [];

        foreach($cleanedCookies as $name => $value) {
            $cookies[$name] = new Cookie($name, $value);
        }

        $this->cookies    = new ReadOnlyMap($cookies);
        $this->newCookies = new Map();
    }

    /**
     * Adds a cookie to the response
     *
     * @param Maverick\Http\Session\Cookie $cookie
     */
    public function addCookie(Cookie $cookie) {
        $this->newCookies->set($cookie->getName(), $cookie);
    }

    /**
     * Deletes a cookie
     *
     * @param Maverick\Http\Session\Cookie $cookie
     */
    public function deleteCookie(Cookie $cookie) {
        if($this->cookies->has($cookie->getName())) {
            $cookie->setExpiration(-10);
            $this->addCookie($cookie);
        }
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

    /**
     * Gets all of the new cookies to be set
     *
     * @return Maverick\DataStructure\ArrayList
     */
    public function getNewCookies() {
        return $this->newCookies;
    }

    /**
     * Gets the redirect message
     *
     * @return string
     */
    public function getRedirectMessage() {
        if($this->cookies->has('flash')) {
            $cookie = $this->cookies->get('flash');
            $this->deleteCookie($cookie);

            return $cookie->getValue();
        }
    }
}