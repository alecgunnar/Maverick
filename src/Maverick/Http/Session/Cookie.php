<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Http\Session\Cookie;

use Maverick\Exception\InvalidArgumentException,
    DateTime;

class Cookie {
    /**
     * The name of the cookie
     *
     * @var string
     */
    private $name;

    /**
     * The value of the cookie
     *
     * @var string
     */
    private $value = '';

    /**
     * The expiration time of the cookie
     *
     * @var DateTime
     */
    private $expire;

    /**
     * The path for the cookie
     *
     * @var string
     */
    private $path = '';

    /**
     * The domain for the cookie
     *
     * @var string
     */
    private $domain = '';

    /**
     * Secure?
     *
     * @var boolean
     */
    private $secure = false;

    /**
     * HTTP Only?
     *
     * @var boolean
     */
    private $httpOnly = false;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $value=''
     * @param int    $expire=0
     * @param string $path=''
     * @param string $domain
     * @param string $secure=false
     * @param string $httpOnly=false
     */
    public function __construct($name, $value='', $expire=0, $path='', $domain='', $secure=false, $httpOnly=false) {
        $this->setName($name);
        $this->setValue($value);
        $this->setExpire($expire);
        $this->setPath($path);
        $this->setDomain($domain);

        $this->secure   = $secure;
        $this->httpOnly = $httpOnly;
    }

    /**
     * Gets the cookie as a string for a HTTP response header
     *
     * @return string
     */
    public function __toString() {
        $cookie = $this->name . '=' . $this->value . ';';

        if($this->domain) {
            $cookie .= ' Domain=' . $this->domain . ';';
        }

        if($this->path) {
            $cookie .= ' Path=' . $this->path . ';';
        }

        if($this->expires !== null) {
            $cookie .= ' Expires=' . $this->expire->format(DateTime::RFC1123) . ';';
        }

        if($this->secure) {
            $cookie .= ' Secure;';
        }

        if($this->httpOnly) {
            $cookie .= ' HttpOnly;';
        }

        return $cookie;
    }

    /**
     * These are getters and setters....
     */
    public function setName($name) {
        if(!is_string($name)) {
            throw new InvalidArgumentException(__METHOD__, 1, ['string']);
        }

        $this->name = $name;

        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setValue($value) {
        if(!is_string($value)) {
            throw new InvalidArgumentException(__METHOD__, 1, ['string']);
        }

         $this->value = $value;

        return $this;
    }

    public function getValue() {
        return $this->value;
    }

    public function setExpiration($expire) {
        if($expire == 0 || $expire === null) {
            $expire = null;
        } elseif($expire instanceof DateTime) {
            $this->expire = $expire;
        } elseif(is_numeric($expire)) {
            $this->expire = new DateTime('now');
            $this->expire->modify($expire . ' seconds');
        } else {
            throw new InvalidArgumentException(__METHOD__, 1, ['DateTime', 'number']);
        }

        return $this;
    }

    public function getExpiration() {
        return $this->expire;
    }

    public function setPath($path) {
        if(!is_string($path)) {
            throw new InvalidArgumentException(__METHOD__, 1, ['string']);
        }

        $this->path = $path;

        return $this;
    }

    public function getPath() {
        return $this->path;
    }

    public function setDomain($domain) {
        if(!is_string($name)) {
            throw new InvalidArgumentException(__METHOD__, 1, ['string']);
        }

        $this->domain = $domain;

        return $this;
    }

    public function getDomain() {
        return $this->domain;
    }

    public function setSecure() {
        $this->secure = !$this->secure;

        return $this;
    }

    public function isSecure() {
        return $this->secure;
    }

    public function setHttpOnly() {
        $this->httpOnly = !$this->httpOnly;

        return $this;
    }

    public function isHttpOnly() {
        return $this->httpOnly;
    }
}