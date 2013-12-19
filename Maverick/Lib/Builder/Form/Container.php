<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

abstract class Builder_Form_Container extends Builder_Form_Component {
    /**
     * The fields
     *
     * @var array $fields
     */
    protected $fields = array();
    
    /**
     * The default field template
     *
     * @var string
     */
    private $defaultFieldTpl = 'Default';

    /**
     * The hidden fields within this container
     * THESE SHOULD ALREADY BE RENDERED
     *
     * @var string
     */
    protected $hiddenFields = '';

    /**
     * Set the default field tpl
     *
     * @param  string $defaultFieldTpl
     * @return self
     */
    public function setDefaultFieldTpl($defaultFieldTpl) {
        $this->defaultFieldTpl = $defaultFieldTpl;

        return $this;
    }

    /**
     * Gets the default field tpl
     *
     * @return string
     */
    public function getDefaultFieldTpl() {
        return $this->defaultFieldTpl;
    }
    
    /**
     * Adds a field to the form
     *
     * @param  string  $type
     * @param  string  $name
     * @return mixed
     */
    public function addField($type, $name) {
        $class = '\Maverick\Lib\Builder_Form_Field_' . $type;

        if(array_key_exists($name, $this->form->fields)) {
            throw new \Exception('You cannot add multiple fields/groups with the same name to the same form.');
        }

        $fieldBuilder = new $class($name);
        $fieldBuilder->setForm($this->form)
            ->setContainer($this);

        $this->fields[$name] = $fieldBuilder;

        return $fieldBuilder;
    }

    /**
     * Adds a group of fields to the form
     *
     * @param  string $name
     * @return \Maverick\Lib\Builder_Form_Field_Group
     */
    public function addFieldGroup($name) {
        if(array_key_exists($name, $this->fields)) {
            throw new \Exception('You cannot add multiple fields/groups with the same name to the same form.');
        }

        $groupBuilder = new \Maverick\Lib\Builder_Form_Field_Group();
        $groupBuilder->setForm($this->form)
            ->setContainer($this)
            ->setName($name);

        $this->fields[$name] = $groupBuilder;

        return $groupBuilder;
    }

    /**
     * Gets the fields
     *
     * @return string
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * Renders a field
     *
     * @param  \Maverick\Lib\Builder_Form_Field
     * @return string
     */
    protected function renderField($field) {
        $fieldVariables = array('label'       => $field->getLabel(),
                                'required'    => $this->form->getRequiredMarker($field->isRequired()),
                                'error'       => $field->getError(),
                                'field'       => $field->render(),
                                'description' => $field->getDescription());

        return \Maverick\Lib\Output::getTplEngine()->getTemplate('Forms/Fields/' . ($field->getTpl() ?: $this->defaultFieldTpl), $fieldVariables);
    }

    

    /**
     * Renders the fields in this container
     *
     * @return string
     */
    protected function renderFields() {
        $formContent = '';

        foreach($this->fields as $name => $field) {
            if($field instanceof \Maverick\Lib\Builder_Form_Field_Group) {
                $formContent .= $field->render();
            } else {
                if($field->isHidden()) {
                    $this->hiddenFields .= $this->renderField($field);
                } else {
                    $formContent .= $this->renderField($field);
                }
            }
        }

        return $formContent;
    }

    /**
     * Renders this container
     *
     * @param  array $variables=array()
     * @return string
     */
    protected function renderContainer($variables=array()) {
        if(!is_array($variables)) {
            throw new \Maverick\Exception\InvalidParameterException('\Maverick\Lib\Builder_Form_Container::renderContainer expects one parameter that is an array. A/An . ' . gettype($variables) . ' was supplied.');
        }

        return \Maverick\Lib\Output::getTplEngine()->getTemplate('Forms/' . $this->tpl, array_merge($this->tplVars, $variables));
    }
}