<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Builder_FormField_TextField extends Builder_FormField {
    /**
     * Sets up the field
     *
     * @param  string $name
     * @param  string $type='text'
     * @return null
     */
    public function __construct($name) {
        parent::__construct('input');

        $this->name = $name;

        $this->addAttribute('name', $name);
    }
}