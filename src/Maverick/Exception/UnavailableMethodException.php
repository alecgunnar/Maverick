<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Exception;

use Exception;

class UnavailableMethodException extends Exception {
    public function __construct($method) {
        parent::__construct($method . ' is not available via this object.');
    }
}