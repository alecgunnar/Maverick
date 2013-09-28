<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Model_Input extends Model {
    /**
     * Gets the input and cleans it up
     *
     * @param  array $input
     * @return null
     */
    public function __construct($input) {
        $this->process($input);
    }

    /**
     * Processes part of the array
     *
     * @param array $input
     */
    public function process($input) {
        foreach($input as $k => $v) {
            if(is_array($v)) {
                $this->set($k, new self($v));
            } else {
                $this->set($k, $v);
            }
        }
    }

    /**
     * Adds a clean version of the input to the model
     *
     * @param  string $name
     * @param  string $value
     */
    public function set($name, $value='') {
        if(is_object($value)) {
            parent::set($name, $value);
        } else {
            parent::set($name, htmlentities(strip_tags($value)));
        }
    }
}