<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Http;

use Maverick\DataStructure\UserInputMap;

class Session {
    /**
     * The cookies sent with this request
     *
     * @var Maverick\DataStructure\ReadOnlyMap
     */
    private $cookies;

    /**
     * Constructor
     */
    public function __construct() {
        $this->cookies = new UserInputMap($_COOKIE);
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