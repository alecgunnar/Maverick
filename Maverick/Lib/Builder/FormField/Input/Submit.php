<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Builder_FormField_Input_Submit extends Builder_FormField_Input {
    /**
     * Sets up the field
     *
     * @param  string $name
     * @return null
     */
    public function __construct($name) {
        parent::__construct($name, 'submit');

        $this->value('Submit');
    }
}