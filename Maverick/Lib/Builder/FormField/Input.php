<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Builder_FormField_Input extends Builder_FormField {
    /**
     * Sets up the field
     *
     * @param  string $name
     * @param  string $type='text'
     * @return null
     */
    public function __construct($name, $type='text') {
        parent::__construct('input', true);

        $this->name = $name;

        $this->isSelfClosing()
            ->addAttribute('type', $type)
            ->addAttribute('name', $name);
    }
}