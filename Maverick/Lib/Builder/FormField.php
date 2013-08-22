<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Builder_FormField extends Builder_Tag {
    /**
     * The name of the field
     *
     * @var string
     */
    public $name = 'not_named';

    /**
     * Is the field required
     *
     * @var boolean
     */
    public $required = false;

    /**
     * The label for the field
     *
     * @var string
     */
    public $label = '';

    /**
     * The description for the field
     *
     * @var string
     */
    public $description = '';

    /**
     * The template for the field
     *
     * @var string
     */
    public $tpl = '';

    /**
     * What should be validated for
     *
     * @var array
     */
    public $validateFor = array();

    /**
     * The attributes for the field
     *
     * @var array $attributes
     */
    protected $attributes = array();

    /**
     * Sets whether the field is required or not
     *
     * @param  booelan $isRequired=true
     * @return self
     */
    public function required($isRequired=true) {
        $this->required = $isRequired ?: true;

        try {
            $this->validate('NotEmpty');
        } catch(\Exception $e) { }

        return $this;
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
     * Add validation constraints
     *
     * @throws \Exception
     * @param  mixed $validator
     * @param  array $args
     * @return self
     */
    public function validate($validator, array $args=null) {
        if(array_key_exists($validator, $this->validateFor)) {
            throw new \Exception('You cannot validate for the same thing twice! You added another ' . $validator . ' validator to ' . $this->name);
        }

        $class = '\Maverick\Lib\Validator_' . $validator;

        $this->validateFor[$validator] = new $class($args);

        return $this;
    }
}