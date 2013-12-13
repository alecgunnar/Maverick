<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Builder_Form_Field extends Builder_Form_Component {
    /**
     * The template
     *
     * @var string
     */
    protected $tpl = '';
    
    /**
     * Is this field hidden
     *
     * @var boolean
     */
    protected $hidden = false;

    /**
     * Auto-fill this field
     *
     * @var boolean
     */
    protected $autoFill = true;

    /**
     * Is the field required
     *
     * @var boolean
     */
    protected $required = false;

    /**
     * The label for the field
     *
     * @var string
     */
    protected $label = '';

    /**
     * The description for the field
     *
     * @var string
     */
    protected $description = '';

    /**
     * What should be validated for
     *
     * @var array
     */
    protected $validateFor = array();

    /**
     * The value of this field
     *
     * @var $value
     */
    protected $value = '';

    /**
     * The errors for this field
     *
     * @var array
     */
    private $errors = array();

    /**
     * Gets the full name of the field, with the namespace if there is one
     *
     * @return string
     */
    public function getFullName() {
        if($this->form->getName()) {
            return $this->form->getName() . '[' . $this->name . ']';
        }

        return $this->name;
    }

    /**
     * Toggles the auto-fill setting
     *
     * @return self
     */
    public function toggleAutoFill() {
        $this->autoFill = $this->autoFill ? false  : true;

        return $this;
    }

    /**
     * Gets whether or not the field has auto-fill enabled
     *
     * @return boolean
     */
    public function isAutoFill() {
        return $this->autoFill;
    }

    /**
     * Gets whether or not this field is hidden
     *
     * @return boolean
     */
    public function isHidden() {
        return $this->hidden;
    }

    /**
     * Sets the field label
     *
     * @param  string $label
     * @return self
     */
    public function setLabel($label) {
        $this->label = $label;

        return $this;
    }

    /**
     * Gets the label
     *
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * Sets the field description
     *
     * @param  string $description
     * @return self
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Gets the description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Gets the tpl variables
     *
     * @return array
     */
    public function getTplVars() {
        return $this->tplVars;
    }

    /**
     * Sets the value for the field
     *
     * @param  string $value
     * @return self
     */
    public function setValue($value) {
        $this->addAttribute('value', $value);

        return $this;
    }

    /**
     * Sets the value of the field to an attribute of the class
     * this will not make the value show up in the HTML form field
     * use value() for that.
     *
     * @param string $value
     */
    public function setSubmittedValue($value) {
        $this->value = $value;
    }

    /**
     * Get the value of the field
     *
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Sets the maximum length for the field
     *
     * @param  integer $maxLength
     * @return self
     */
    public function setMaxLength($maxLength) {
        $this->addAttribute('maxlength', $maxLength);

        return $this;
    }

    /**
     * Sets whether the field is required or not
     *
     * @param  string $errorMessage
     * @return self
     */
    public function required($errorMessage='') {
        $this->required = true;

        $this->validate('NotEmpty', $errorMessage);

        return $this;
    }

    /**
     * Gets whether or not the field is required
     *
     * @return boolean
     */
    public function isRequired() {
        return $this->required;
    }

    /**
     * Add validation constraints
     *
     * @throws \Exception
     * @param  mixed  $validator
     * @param  string $message=''
     * @param  array  $args
     * @return self
     */
    public function validate($validator, $message='') {
        $class = '\Maverick\Lib\Validator_' . $validator;

        $this->validateFor[$validator] = new $class($message);

        return $this;
    }

    /**
     * Gets the requred validation methods
     *
     * @return array
     */
    public function getValidateFor() {
        return $this->validateFor;
    }

    /**
     * Adds an error to this field
     *
     * @param string $error
     */
    public function setError($error) {
        $this->errors[] = $error;
    }

    /**
     * Gets the first error for this field
     *
     * @return string
     */
    public function getError() {
        if(count($this->errors)) {
            return $this->errors[0];
        }

        return null;
    }

    /**
     * Gets all of the errors for this field
     *
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Renders the field
     *
     * @return string
     */
    public function render() {
        if($this->form->getName()) {
            $this->addAttribute('name', $this->getFullName());
        }

        return parent::render();
    }
}