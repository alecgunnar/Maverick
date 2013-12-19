<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Builder_Form_Field_Input_Radio extends Builder_Form_Field_Input {
    /**
     * The options provided with these radio buttons
     *
     * @var array
     */
    private $options = array();

    /**
     * Put the label before or after the button?
     *
     * @var boolean
     */
    private $labelAfter = true;

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
     * @param  string $value
     * @param  string $label=''
     * @return self
     */
    public function addOption($value, $label='') {
        $this->options[$value] = $label;

        return $this;
    }

    /**
     * Put the label before the button
     *
     * @return self
     */
    public function labelBefore() {
        $this->labelAfter = false;

        return $this;
    }

    /**
     * Put the label after the button
     *
     * @return self
     */
    public function labelAfter() {
        $this->labelAfter = true;

        return $this;
    }

    /**
     * Renders this field
     *
     * @throws \Exception
     * @return string
     */
    public function render() {
        if(!count($this->options)) {
            throw new \Exception('No options were added to field: "' . $this->name . '"');
        }

        $buttons = '';
        $i       = 0;

        foreach($this->options as $value => $label) {
            $id = $this->name . '_opt_' . $i;

            $field = new Builder_Form_Field_Input($this->name, 'radio');
            $field->setValue($value)
                ->setForm($this->form)
                ->addAttribute('id', $id)
                ->setContainer($this->container);

            if($this->getActualValue() == $value) {
                $field->addAttribute('checked', '');
            }

            if($this->labelAfter) {
                $buttons .= $field->render();
            }

            if($label) {
                $labelTag = new Builder_Tag('label');
                $labelTag->addAttribute('for', $id)
                    ->setContent($label);

                $buttons .= $labelTag->render();
            }

            if(!$this->labelAfter) {
                $buttons .= $field->render();
            }

            $i++;
        }

        return $buttons;
    }
}