<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Builder_Form_Field_Select extends Builder_Form_Field {
    /**
     * The options provided with these radio buttons
     *
     * @var array
     */
    private $options = array();

    /**
     * Is a multiple select field
     *
     * @var boolean
     */
    private $multiple = false;

    /**
     * Sets up the field
     *
     * @param string $name
     */
    public function __construct($name) {
        $this->name = $name;
    }

    /**
     * Determines if this checkbox is checked
     *
     * @param  string $value
     * @return self
     */
    public function value($value) {
        $this->value = $value;

        return $this;
    }

    /**
     * Adds options to this field
     *
     * @param  array $options
     * @return self
     */
    public function addOptions($options) {
        if(count($options)) {
            foreach($options as $value => $label) {
                $this->addOption($value, $label);
            }
        }

        return $this;
    }

    /**
     * Adds options to this field
     *
     * @param  string        $value
     * @param  string| array $label
     * @return self
     */
    public function addOption($value, $label='') {
        $this->options[$value] = $label;

        return $this;
    }
    

    /**
     * Add option group
     *
     * @param  string $label
     * @param  array  $options
     * @return self
     */
    public function addGroup($label, $options) {
        $this->addOption($label, $options);

        return $this;
    }

    /**
     * Sets this as a multiple select field
     *
     * @return self
     */
    public function multiple() {
        $this->multiple = true;

        return $this;
    }

    /**
     * Renders this field
     *
     * @return string
     */
    public function render() {
        $options = $this->renderOptions($this->options);

        $multi = '';

        if($this->multiple) {
            $multi = '[]';
        }

        $select = new Builder_Tag('select');
        $select->addAttribute('name', $this->getFullName() . $multi)
            ->setContent($options);

        if($this->multiple) {
            $select->addAttribute('multiple', '');
        }

        return $select->render();
    }

    /**
     * Renders a group of options
     *
     * @param  array $options
     * @return string
     */
    private function renderOptions($options) {
        $opts = '';

        if(count($options)) {
            foreach($options as $value => $label) {
                $opt = new Builder_Tag('option');
                $opt->addAttribute('value', $value)
                    ->setContent($label);
    
                if($this->isSelected($value)) {
                    $opt->addAttribute('selected', '');
                }
    
                if(is_array($label)) {
                    $optGroup = new Builder_Tag('optgroup');
                    $optGroup->addAttribute('label', $value)
                        ->setContent($this->renderOptions($label));
    
                    $opts .= $optGroup->render();
                } else {
                    $opts .= $opt->render();
                }
            }
        }

        return $opts;
    }

    /**
     * Checks if a value is selected
     *
     * @param string $value
     */
    private function isSelected($value) {
        if($this->getActualValue() instanceof \Maverick\Lib\Model_Input) {
            if(array_key_exists($value, array_flip($this->getActualValue()->getAsArray()))) {
                return true;
            }
        } else {
            if($value == $this->getActualValue()) {
                return true;
            }
        }

        return false;
    }
}