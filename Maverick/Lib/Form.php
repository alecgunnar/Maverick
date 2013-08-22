<?php

/**
 * @package Maverick
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

abstract class Form extends \Maverick\Lib\Builder_Form {
    /**
     * The model of the form input
     *
     * @var \Maverick\Lib\Model_Input | null $input
     */
    protected $input = null;

    /**
     * The errors for this form
     *
     * @var array
     */
    protected $errors = array();    

    /**
     * Sets up the form and checks it
     *
     * @return null
     */
    public function __construct() {
        $this->build();
        $this->process();
    }

    /**
     * Processes the form
     *
     * @return null
     */
    public function process() {
        if($this->getStatus()) {
            $input = $this->getModel();

            foreach($this->fields as $name => $builder) {
                if(count($builder->validateFor)) {
                    foreach($builder->validateFor as $type => $validator) {
                        $validator->value = $input->get($name);

                        if(!$validator->isValid()) {
                            $this->setFieldError($name, $validator->errorMessage);
                        }
                    }
                }
            }
        }
    }

    /**
     * Gets the status of the form submission
     *
     * @return boolean
     */
    public function getStatus() {
        if(count($this->errors)) {
            return false;
        }

        if(strtolower($_SERVER['REQUEST_METHOD']) == strtolower($this->method)) {
            $input = $this->getModel();

            foreach($this->fields as $name => $builder) {
                if(is_null($input->get($name))) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Gets the sumbmission model
     *
     * @return \Maverick\Lib\Model_Input
     */
    public function getModel() {
        if(!is_null($this->input)) {
            return $this->input;
        }

        $raw   = strtolower($this->method) == 'post' ? $_POST : $_GET;
        $input = new \Maverick\Lib\Model_Input($raw);

        $this->input = $input;

        return $input;
    }

    /**
     * Sets an error for a field
     *
     * @param  string $fieldName
     * @param  string $error
     * @return null
     */
    public function setFieldError($fieldName, $error) {
        $this->errors[$fieldName][] = $error;
    }

    /**
     * Returns any error for the field
     *
     * @param  string  $fieldName
     * @param  boolean $getAll
     * @return string | array | null
     */
    public function getFieldError($fieldName, $getAll=false) {
        if(!array_key_exists($fieldName, $this->errors)) {
            return null;
        }

        if(!$getAll) {
            return $this->errors[$fieldName][0];
        }

        return $this->errors[$fieldName];
    }

    /**
     * Builds the form to be rendered later
     *
     * @return null
     */
    abstract public function build();

    /**
     * Validates the form
     *
     * @return boolean
     */
    abstract public function validate();
}