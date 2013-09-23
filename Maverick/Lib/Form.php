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
     * @var \Maverick\Lib\Model_Input | null
     */
    protected $input = null;  

    /**
     * Was this form submitted
     *
     * @var boolean | null
     */
    private $status = null;

    /**
     * Was the submission valid?
     *
     * @var boolean | null
     */
    private $isValid = null;

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
     * @throws \Exception
     */
    private function process() {
        if(is_null($this->isValid)) {
            if($this->getStatus()) {
                $input = $this->getModel();

                if($this->submissionTokenEnabled() && $input->get('formSubmissionToken') != $_SESSION[$this->getName() . '_submission_token']) {
                    throw new \Exception('Possible CSRF attempt');
                }

                foreach($this->getFields() as $name => $builder) {
                    if(count(($validateFor = $builder->getValidateFor()))) {
                        foreach($validateFor as $type => $validator) {
                            $validator->setValue($input->get($name));

                            if(!$validator->isValid()) {
                                $this->setFieldError($name, $validator->getErrorMessage());

                                $this->isValid = false;
                            }
                        }
                    }
                }

                if(!$this->validate()) {
                    $this->isValid = false;
                }

                if(is_null($this->isValid)) {
                    $this->isValid = true;
                }
            } else {
                $this->isValid = false;
            }
        }

        return $this->isValid;
    }

    /**
     * Gets the status of the form submission
     *
     * true  = all fields were submitted
     * false = some or none of the fields were submitted
     *
     * @return boolean
     */
    public function getStatus() {
        if(is_null($this->status)) {
            if(strtolower($_SERVER['REQUEST_METHOD']) == strtolower($this->getMethod())) {
                $input = $this->getModel();
    
                foreach($this->getFields() as $name => $builder) {
                    if(is_null($input->get($name))) {
                        $this->status = false;
                    }
                }

                if(is_null($this->status)) {
                    $this->status = true;
                }
            } else {
                $this->status = false;
            }
        }

        return $this->status;
    }

    /**
     * Gets the status of the form submission
     *
     * return boolean
     */
    public function isSubmissionValid() {
        if($this->status && $this->isValid) {
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

        $raw   = strtolower($this->getMethod()) == 'post' ? $_POST : $_GET;
        $input = new \Maverick\Lib\Model_Input($raw);

        if($this->getName()) {
            $input = $input->get($this->getName()) ?: $input;
        }

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
    abstract protected function validate();

    /**
     * Submits the form
     *
     * @return mixed
     */
    abstract public function submit();
}