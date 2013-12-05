<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Exception;

class InvalidParameterException extends \Exception {
    public function __construct($message='') {
        parent::__construct($message ?: 'An invalid parameter was supplied');
    }
}