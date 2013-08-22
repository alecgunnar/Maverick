<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Validator_IsEmail extends Validator {
    /**
     * The error message for an empty field
     *
     * @var string
     */
    public $errorMessage = 'You must enter an email address';

    /**
     * Validates the field
     *
     * @return boolean
     */
    public function isValid() {
        if(pre_match('~([a-z0-9])@([a-z\-\.{2,5}).([a-z\-]{2,5})~i', $this->value)) {
            return true;
        }

        return false;
    }
}