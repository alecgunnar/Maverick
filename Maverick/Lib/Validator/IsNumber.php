<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Validator_IsNumber extends Validator {
    /**
     * The error message for an empty field
     *
     * @var string
     */
    public $errorMessage = 'You must enter a number';

    /**
     * Validates the field
     *
     * @return boolean
     */
    public function isValid() {
        if(!$this->value || is_numeric($this->value)) {
            return true;
        }

        return false;
    }
}