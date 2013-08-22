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
    public $errorMessage = 'The input for this field was invalid.';

    /**
     * The value which needs to be validated
     *
     * @var string | integer | null
     */
    public $value = null;

    /**
     * Setup values for the field
     *
     * @var array
     */
    protected $vars = array();

    /**
     * Gets the setup variables for the validator
     *
     * @param  array $vars
     * @return null
     */
    public function __construct(array $vars=null) {
        if(is_array($vars)) {
            $this->vars = array_merge($this->vars, $vars);
        }
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