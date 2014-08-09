<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Exception;

use Exception;

class InvalidArgumentException extends Exception {
    public function __construct($method, $paramNum, $expectedTypes) {
        parent::__construct($method . ' expects parameter #' . $paramNum . ' to be a: ' . implode(',', $expectedTypes));
    }
}