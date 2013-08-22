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
        foreach($input as $k => $v) {
            $this->set($k, $v);
        }
    }

    /**
     * Adds a cookie to the model
     *
     * @param  string $name
     * @param  string $value
     */
    public function set($name, $value='') {
        $this->data[$name] = htmlentities(strip_tags($value));
    }
}