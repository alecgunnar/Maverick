<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Builder_Form extends Builder_Form_Container {
    /**
     * The method of the form
     *
     * @var string $method
     */
    private $method = 'post';

    /**
     * The action of the form
     *
     * @var string $action
     */
    private $action = '';

    /**
     * The encoding type of the form
     *
     * @var string | null
     */
    private $encType = null;

    /**
     * The container for the fields
     *
     * @var mixed
     */
    private $formContainer = null;

    /**
     * Whether or not to have a submission token
     *
     * @var boolean $submissionToken
     */
    private $submissionToken = null;

    /**
     * Sets up the form
     */
    public function __construct() {
        $this->action = Router::getUri()->getUri();
        $this->form   = $this;

        if(!session_id()) {
            $this->submissionToken = false;
        }
    }

    /**
     * Sets the name of the form
     *
     * @param string $name
     */
    protected function setName($name) {
        $this->name = $name;

        if(is_null($this->submissionToken)) {
            $this->toggleSubmissionToken();
        }
    }

    /**
     * Sets the method of the form
     *
     * @param string $method
     */
    protected function setMethod($method) {
        $this->method = $method;
    }

    /**
     * Get the method of the field
     *
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Sets the action of the form
     *
     * @param string $action
     */
    protected function setAction($action) {
        $this->action = $action;
    }

    /**
     * Get the action of the field
     *
     * @return string
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * Enables file uploads
     */
    protected function allowFileUploads() {
        $this->encType = 'multipart/form-data';
    }

    /**
     * Sets the form container
     *
     * @param mixed $container
     */
    protected function setContainer($container) {
        $this->formContainer = $container;
    }

    /**
     * Changes the required field marker
     *
     * @param string $requiredId
     */
    protected function setRequiredMarker($requiredId) {
        $this->requiredId = $requiredId;
    }

    /**
     * Gets the required field marker if needed
     *
     * @param  boolean $isRequired=true
     * @return string
     */
    public function getRequiredMarker($isRequired=true) {
        if($isRequired) {
            return $this->requiredId;
        }

        return '';
    }

    /**
     * Adds an ID field to help prevent CSRF
     */
    private function addAntiCSRFToken() {
        $token = $this->input->get('formSubmissionToken') ?: \Maverick\Lib\Utility::generateToken(25);

        $_SESSION[$this->name . '_submission_token'] = $token;

        $this->addField('Input_Hidden', 'formSubmissionToken')
            ->setValue($token);
    }

    /**
     * Toggle the submission token feature
     */
    public function toggleSubmissionToken() {
        if(session_id()) {
            $this->submissionToken = (is_null($this->submissionToken) || !$this->submissionToken) ? true : false;
        }
    }

    /**
     * Says whether or not the submission token feature is enabled
     */
    public function submissionTokenEnabled() {
        if($this->name) {
            return $this->submissionToken;
        }

        return false;
    }

    /**
     * Adds a hidden check field to the form
     */
    private function addCheckField() {
        $this->addField('Input_Hidden', $this->name . '_submit')
            ->setValue('submitted');
    }

    /**
     * Adds rendered hidden fields to the form
     *
     * @param string $hiddenFields
     */
    public function addHiddenFields($hiddenFields) {
        $this->hiddenFields .= $hiddenFields;
    }

    /**
     * Renders the form for the controller
     *
     * @throws \Exception
     * @return string
     */
    public function render() {
        if(!count($this->fields)) {
            throw new \Exception('No fields have been added to the form');
        }

        if(!$this->name) {
            throw new \Exception('No name was set for the form');
        }

        if($this->submissionTokenEnabled()) {
            $this->addAntiCSRFToken();
        }

        $this->addCheckField();

        $this->setType('form');
        $this->addAttributes(array('name'   => $this->name,
                                   'method' => $this->method,
                                   'action' => $this->action));

        if($this->encType) {
            $this->addAttribute('enc-type', $this->encType);
        }

        $this->addContent($this->renderFields() . $this->hiddenFields);

        return parent::renderContainer(array('form' => parent::render()));
    }
}