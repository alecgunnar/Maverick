<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

abstract class Validator {
    /**
     * The default (crappy) error message
     *
     * This should be redefined in all child classes
     *
     * @var string
     */
    protected $errorMessage = 'The input for this field was invalid.';

    /**
     * The value which needs to be validated
     *
     * @var string | integer | null
     */
    protected $value = null;

    /**
     * Gets the setup variables for the validator
     *
     * @param string $message
     */
    public function __construct($message='') {
         if($message) {
             $this->errorMessage = $message;
         }
    }

    /**
     * Sets the error message
     *
     * @param string $errorMessage
     */
    public function setErrorMessage($errorMessage) {
        $this->errorMessage = $errorMessage;
    }

    /**
     * Gets the error message
     *
     * @return string
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    }
    
    /**
     * Sets the value to be checked
     *
     * @param string $value
     */
    public function setValue($value) {
        $this->value = $value;
    }

    /**
     * Tests a sting to see if it is a match for the given validator
     *
     * @param $validator
     * @param $value
     * @return boolean
     */
    public static function test($validator, $value) {
        $class = __NAMESPACE__ . '\Validator_' . $validator;
        $inst  = new $class;

        return $inst->setValue($value)->isValid();
    }

    /**
     * Does the actual validation
     *
     * TRUE if input is valid, FALSE otherwise
     *
     * @return boolean
     */
    abstract public function isValid();
}