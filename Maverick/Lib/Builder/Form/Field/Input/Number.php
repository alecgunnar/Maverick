<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Builder_Form_Field_Input_Number extends Builder_Form_Field_Input_Text {
    /**
     * Sets up the field
     *
     * @param  string $name
     * @return null
     */
    public function __construct($name) {
        parent::__construct($name, 'number');

        $this->validate('IsNumber');
    }
}