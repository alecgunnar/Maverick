<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Builder_FormField extends Builder_Tag {
    /**
     * Then namespace for this field
     *
     * @var string
     */
    private $ns = '';

    /**
     * The name of the field
     *
     * @var string
     */
    protected $name = '';

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
     * The template for the field
     *
     * @var string
     */
    protected $tpl = '';

    /**
     * What should be validated for
     *
     * @var array
     */
    protected $validateFor = array();

    /**
     * The errors for this field
     *
     * @var array
     */
    private $errors = array();

    /**
     * Sets the namespace for the field
     *
     * @param string $namespace
     */
    public function setNamespace($namespace) {
        $this->ns = $namespace;
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
     * Toggles the auto-fill setting
     *
     * @return self
     */
    public function toggleAutoFile() {
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
    public function label($label) {
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
    public function description($description) {
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
     * Sets the field tpl
     *
     * @param  string $tpl
     * @return self
     */
    public function tpl($tpl) {
        $this->tpl = $tpl;

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
     * Sets the value for the field
     *
     * @param  string $value
     * @return self
     */
    public function value($value) {
        $this->addAttribute('value', $value);

        return $this;
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
    public function maxLength($maxLength) {
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
     * Renders the field
     *
     * @return string
     */
    public function render() {
        if($this->ns) {
            $this->addAttribute('name', $this->ns . '[' . $this->name . ']');
        }

        return parent::render();
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
}