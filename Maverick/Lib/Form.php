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
     * The file upload data
     *
     * @var \Maverick\Model\Input | null
     */
    private $fileUploadData = null;

    /**
     * The errors for this form
     *
     * @var array
     */
    protected $errors = array();

    /**
     * Sets up the form
     */
    public function __construct() {
        parent::__construct();

        $this->build();
        $this->process();
    }

    /**
     * Processes the form
     */
    private function process() {
        if(is_null($this->isValid)) {
            if($this->getStatus()) {
                $isValid = $this->checkFields($this, $this->getModel());

                if($this->validate() === false || count($this->errors)) {
                    $this->isValid = false;
                } elseif(is_null($this->isValid)) {
                    $this->isValid = true;
                }
            } else {
                $this->isValid = false;
            }
        }

        return $this->isValid;
    }

    /**
     * Processes all of the fields in a container
     *
     * @param  \Maverick\Lib\Builder_Form_Container $container
     * @param  \Maverick\Lib\Model_Input $input
     * @return boolean;
     */
    private function checkFields($container, $input) {
        $isValid = true;

        foreach($container->getFields() as $name => $field) {
            if($field instanceof \Maverick\Lib\Builder_Form_Field_Group) {
                $isValid = $this->checkFields($field, $input->get($name));
            } else {
                $field->setSubmittedValue($input->get($name));

                if(count(($validateFor = $field->getValidateFor()))) {
                    foreach($validateFor as $type => $validator) {
                        $validator->setValue($field->getSubmittedValue());

                        if(!$validator->isValid()) {
                            $this->setFieldError($name, $validator->getErrorMessage());

                            $isValid = false;
                        }
                    }
                }
            }
        }

        return $isValid;
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
        $input = $this->getModel();

        if($input->get($this->name . '_submit') == 'submitted') {
            $this->status = true;

            if($this->submissionTokenEnabled()) {
                if($input->get('formSubmissionToken') != $_SESSION[$this->name . '_submission_token']) {
                    $this->status = false;
                }
            }
        } else {
            $this->status = false;
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
        if(is_null($this->input)) {
            if(!$this->name) {
                throw new \Exception('You must give this form a name before you can get its input.');
            }

            $raw   = strtolower($this->getMethod()) == 'post' ? $_POST : $_GET;
            $input = new \Maverick\Lib\Model_Input($raw);

            $this->input = $input->get($this->name) ? $this->buildContainerModel($this, $input->get($this->name)) : $input;
        }

        return $this->input;
    }

    /**
     * Builds a model for the container
     *
     * @param  \Maverick\Lib\Builder_Form_Container $container
     * @parma  \Maverick\Lib\Model_Input $input
     * @return \Maverick\Lib\Model_Input
     */
    private function buildContainerModel($container, $input) {
        foreach($container->getFields() as $name => $field) {
            if($field instanceof \Maverick\Lib\Builder_Form_Field_Group) {
                if(is_null($input->get($name))) {
                    $input->set($name, new \Maverick\Lib\Model_Input(array()));
                }

                $input->set($name, $input->get($name));
            }
        }

        return $input;
    }

    /**
     * Gets the uploaded files data
     *
     * @return \Maverick\Lib\Model_Input
     */
    public function getFilesModel() {
        $files = $_FILES[$this->name];
        $data  = array();

        if(count($files['name'])) {
            foreach($files['name'] as $field => $name) {
                $data[$field] = array('name'     => $name,
                                      'type'     => $files['type'][$field],
                                      'tmp_name' => $files['tmp_name'][$field],
                                      'error'    => $files['error'][$field],
                                      'size'     => $files['size'][$field]);
            }
        }

        return new \Maverick\Lib\Model_Input($data);
    }

    /**
     * Sets an error for a field
     *
     * @throws \Exception
     * @param  string $fieldName
     * @param  string $error
     * @return null
     */
    public function setFieldError($fieldName, $error) {
        $fields = $this->getFields();

        $this->errors[$fieldName][] = $error;

        if(($field = $this->findField($fieldName, $this))) {
            $field->setError($error);

            return;
        } else {
            throw new \Exception($fieldName . ' does not exist');
        }
    }

    /**
     * Finds the object of a field to set the error to
     *
     * @param  string $fieldName
     * @param  \Maverick\Lib\Builder_Form_Container $container
     * @return \Maverick\Lib\Builder_Form_Field | boolean
     */
    private function findField($fieldName, $container) {
        foreach($container->fields as $name => $field) {
            if($field instanceof \Maverick\Lib\Builder_Form_Field_Group) {
                if(($field = $this->findField($fieldName, $field))) {
                    return $field;
                }
            } else {
                if($name == $fieldName) {
                    return $field;
                }
            }
        }

        return false;
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
    abstract protected function build();

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