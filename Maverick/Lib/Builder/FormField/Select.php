<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Builder_FormField_Select extends Builder_FormField {
    /**
     * Options for this field
     *
     * @var array
     */
    private $options = array();

    /**
     * Sets up the field
     *
     * @param  string $name
     * @return null
     */
    public function __construct($name) {
        parent::__construct('select', true);
    }

    /**
     * Adds options to the field
     *
     * @param  string | array $value
     * @param  string         $label
     * @return self
     */
    public function addOption($value, $label='') {
        if(is_array($value)) {
            $this->options = array_merge($this->options, $options);
        } else {
            $this->options[$value] = $label;
        }

        return $this;
    }

    /**
     * Renders the select field
     *
     * @return string
     */
    public function render() {
        $options = $this->renderOptions($this->options);

        parent::addContent($options);

        return parent::render();
    }

    /**
     * Renders the options
     *
     * @param  array $opts
     * @return string
     */
    public function renderOptions($opts) {
        $returnOptions = '';

        if(count($opts)) {
            foreach($opts as $v => $l) {
                if(is_array($l)) {
                    $returnOptions .= '<optgroup label="' . $v . '">' . $this->renderOptions($l) . '</optgroup>';
                } else {
                    $returnOptions .= '<option value="' . $v . '">' . $l . '</option>';
                }
            }
        }

        return $returnOptions;
    }

    /**
     * Makes this a multi-select field
     *
     * @return null
     */
    public function multiple() {
        $this->addAttribute('multiple', 'multiple');
    }
}