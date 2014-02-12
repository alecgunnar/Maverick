<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Builder_Form_Field extends Builder_Form_Component {
    /**
     * The field this field is attached to, if it is attached to any field
     *
     * @var \Maverick\Lib\Builder_Form_Field | null
     */
    private $parentField = null;

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
    protected $value = null;

    /**
     * The submitted value of the field
     *
     * @var string | array
     */
    protected $submittedValue = null;

    /**
     * The errors for this field
     *
     * @var array
     */
    private $errors = array();

    /**
     * "Junk" that will come before the field
     *
     * @var string
     */
    private $prepend = '';

    /**
     * "Junk" that will come after the field
     *
     * @var string
     */
    private $append = '';

    /**
     * Fields which are attached to this one
     *
     * @var array
     */
    private $attachedFields = array();

    /**
     * Gets the full name of the field
     *
     * @return string
     */
    public function getFullName() {
        $parent    = $this->container;
        $namespace = '';
        $last      = '';

        while($parent != null) {
            if($last) {
                $namespace = '[' . $last . ']' . $namespace;
            }

            $last      = $parent->getName();
            $parent    = $parent->getContainer();
        }

        return $last . $namespace . '[' . $this->name . ']';
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
        $this->value = $value;

        return $this;
    }

    /**
     * Gets the value of the field
     *
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Sets the value of the field to an attribute of the class
     * this will not make the value show up in the HTML form field
     * use setValue() for that.
     *
     * @param string $value
     */
    public function setSubmittedValue($value) {
        $this->submittedValue = $value;
    }

    /**
     * Gets the submitted value of the field
     *
     * @return string
     */
    public function getSubmittedValue() {
        return $this->submittedValue;
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
     * Adds a placeholder to the field
     *
     * @param  string $placeholder
     * @return self
     */
    public function setPlaceholder($placeholder) {
        $this->addAttribute('placeholder', $placeholder);

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
     * @param  \Maverick\Lib\Validator | string $validator
     * @param  string $message=''
     * @param  array  $args
     * @return self
     */
    public function validate($validator, $message='') {
        if($validator instanceof \Maverick\Lib\Validator) {
            $inst = $validator;

            $splitClassName = explode('_', get_class($validator));
            unset($splitClassName[0]);

            $validator = implode('_', $splitClassName);
        } else {
            $class = '\Maverick\Lib\Validator_' . $validator;
            $inst  = new $class($message);
        }

        $this->validateFor[$validator] = $inst;

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
     * Adds "junk" to be prepended
     *
     * @param  string $str
     * @return self
     */
    public function prepend($str) {
        $this->prepend .= $str;

        return $this;
    }

    /**
     * Gets the prepended "junk"
     *
     * @return string
     */
    public function getPrepended() {
        return $this->prepend;
    }

    /**
     * Adds "junk" to be appended
     *
     * @param  string $str
     * @return self
     */
    public function append($str) {
        $this->append .= $str;

        return $this;
    }

    /**
     * Gets the prepended "junk"
     *
     * @return string
     */
    public function getAppended() {
        return $this->append;
    }

    /**
     * Attaches a field to this field
     *
     * @throws \Exception
     * @param  string $type
     * @param  string $name
     * @return \Maverick\Lib\Builer_Form_Field
     */
    public function attach($type, $name) {
        $this->form->registerField($name);

        $class = '\Maverick\Lib\Builder_Form_Field_' . $type;

        $parent = $this;

        if(!is_null($this->parentField)) {
            $parent = $this->parentField;
        }

        $fieldBuilder = new $class($name);
        $fieldBuilder->setForm($parent->getForm())
            ->setContainer($parent->getContainer())
            ->setParentField($parent);

        $parent->addAttachedField($name, $fieldBuilder);

        return $fieldBuilder;
    }

    /**
     * Adds an attached field to this field
     *
     * @param string $name
     * @param \Maverick\Lib\Builder_Form_Field $builder
     */
    protected function addAttachedField($name, $builder) {
        $this->attachedFields[$name] = $builder;
    }

    /**
     * Gets the attached fields for this field
     *
     * @return array
     */
    public function getAttachedFields() {
        return $this->attachedFields;
    }

    /**
     * Sets the parent field
     *
     * @param  \Maverick\Lib\Builder_Form_Field $parent
     * @return self
     */
    protected function setParentField($parent) {
        $this->parentField = $parent;
    }

    /**
     * Gets the parent field
     *
     * @return \Maverick\Lib\Builder_Form_Field | null
     */
    public function getParentField() {
        return $this->parentField;
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
     * Gets the value of the field based on wheter or not something was submitted for it
     *
     * @return string
     */
    public function getActualValue() {
        if(!is_null($this->submittedValue) && $this->autoFill) {
            return $this->submittedValue;
        }

        return $this->value;
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

        if($this->getActualValue()) {
            $this->addAttribute('value', $this->getActualValue());
        }

        return parent::render();
    }
}