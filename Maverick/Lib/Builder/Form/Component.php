<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Builder_Form_Component extends Builder_Tag {
    /**
     * The form this field is a part of
     *
     * @var \Maverick\Lib\Form | null
     */
    protected $form = null;

    /**
     * The name
     *
     * @var string
     */
    protected $name = '';

    /**
     * The template
     *
     * @var string
     */
    protected $tpl = 'Default';

    /**
     * The variables for the template
     *
     * @var array
     */
    protected $tplVars = array();

    /**
     * Sets the form for this field
     *
     * @param  string $form
     * @return self
     */
    public function setForm($form) {
        $this->form = $form;

        return $this;
    }

    /**
     * Gets the form this field is a part of
     *
     * @return string
     */
    public function getForm() {
        return $this->form;
    }

    /**
     * Sets the name of the form
     *
     * @param string $name
     */
    protected function setName($name) {
        $this->name = $name;
    }

    /**
     * Gets the name of the field
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the field tpl
     *
     * @param  string $tpl
     * @param  array  $vars
     * @return self
     */
    public function setTpl($tpl, array $vars=null) {
        $this->tpl = $tpl;

        if(count($vars)) {
            $this->tplVars = $vars;
        }

        return $this;
    }

    /**
     * Gets the tpl
     *
     * @return string
     */
    public function getTpl() {
        return $this->tpl;
    }

    /**
     * Sets a tpl variable for this form's tpl
     *
     * @param string $name
     * @param mixed  $value
     */
    public function setTplVariable($name, $value) {
        $this->tplVars[$name] = $value;
    }

    /**
     * Sets multiple tpl variables for this form's tpl
     *
     * @param array $vars
     */
    public function setTplVariables(array $vars) {
        $this->tplVars = array_merge($this->tplVars, $vars);
    }
}