<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Builder_Form_Field_Input_Text extends Builder_Form_Field_Input {
    /**
     * Sets up the field
     *
     * @param  string $name
     * @param  string $type=''
     * @return null
     */
    public function __construct($name, $type='') {
        parent::__construct($name, $type ?: 'text');
    }

    /**
     * Sets the size attribute for this field
     *
     * @param  integer $size
     * @return self
     */
    public function setSize($size) {
        $this->addAttribute('size', $size);

        return $this;
    }
}