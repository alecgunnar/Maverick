<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Builder_Form_Field_Input_Email extends Builder_Form_Field_Input_Text {
    /**
     * Sets up the field
     *
     * @param  string $name
     * @return null
     */
    public function __construct($name) {
        parent::__construct($name, 'email');

        $this->validate('IsEmail');
    }
}