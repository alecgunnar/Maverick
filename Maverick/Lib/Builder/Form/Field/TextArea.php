<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Builder_Form_Field_TextArea extends Builder_Form_Field {
    /**
     * Sets up the field
     *
     * @param  string $name
     * @param  string $type='text'
     * @return null
     */
    public function __construct($name) {
        $this->setType('textarea');

        $this->name = $name;

        $this->addAttribute('name', $name);
    }
    
    /**
     * Sets the value for this field
     *
     * @param  string $value
     * @return self
     */
    public function value($value) {
        $this->setContent($value);

        return $this;
    }
}