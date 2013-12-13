<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Builder_Form_Field_Group extends Builder_Form_Container {
    /**
     * The label for the group
     *
     * @var string
     */
    private $label = '';

    /**
     * The constructor
     *
     * @param string $label=''
     */
    public function __construct($label='') {
        $this->tpl   = 'Group';
        $this->label = $label;
    }

    /**
     * Sets the label for this field group
     *
     * @param  string $label
     * @return self
     */
    public function setLabel($label) {
        $this->label = $label;

        return $this;
    }

    /**
     * Gets the label for this field group
     *
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * Renders the field group
     *
     * @return string
     */
    public function render() {
        $this->form->addHiddenFields($this->hiddenFields);

        return parent::renderContainer(array('label'  => $this->label,
                                             'fields' => $this->renderFields()));
    }
}