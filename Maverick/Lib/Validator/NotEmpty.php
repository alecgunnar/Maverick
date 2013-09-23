<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Validator_NotEmpty extends Validator {
    /**
     * The error message for an empty field
     *
     * @var string
     */
    protected $errorMessage = 'You must enter a value for this field';

    /**
     * Validates the field
     *
     * @return boolean
     */
    public function isValid() {
        if($this->value) {
            return true;
        }

        return false;
    }
}