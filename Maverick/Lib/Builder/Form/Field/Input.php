<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Builder_Form_Field_Input extends Builder_Form_Field {
    /**
     * Sets up the field
     *
     * @param  string $name
     * @param  string $type='text'
     * @return null
     */
    public function __construct($name, $type='text') {
        $this->name = $name;
        
        $this->setType('input');
        $this->isSelfClosing()
            ->addAttribute('type', $type)
            ->addAttribute('name', $name);
    }
}