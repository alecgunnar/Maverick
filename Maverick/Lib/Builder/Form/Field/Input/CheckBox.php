<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Builder_Form_Field_Input_CheckBox extends Builder_Form_Field_Input {
    /**
     * Sets up the field
     *
     * @param string $name
     */
    public function __construct($name) {
        parent::__construct($name, 'checkbox');
    }

    /**
     * Sets whether this check box is checked or not
     *
     * @return self
     */
    public function checked() {
        $this->addAttribute('checked', '');

        return $this;
    }

    /**
     * Determines if this checkbox is checked
     *
     * @param  string $value
     * @return self
     */
    public function checkedValue($value) {
        if($value == $this->value) {
            $this->checked();
        } elseif($this->autoFill) {
            $this->removeAttribute('checked');
        }

        return $this;
    }

    /**
     * Determines if this was submitted checked
     *
     * @param  string $value
     * @return self
     */
    public function setSubmittedValue($value) {
        $this->checkedValue($value);

        return $this;
    }

    /**
     * Adds a label to the checkbox itself
     *
     * @param  string $label
     * @return self
     */
    public function addLabel($label) {
        return $this;
    }
}