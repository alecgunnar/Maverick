<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Builder_Form {
    /**
     * The name of the form
     *
     * @var string $name
     */
    private $name = '';

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
     * The default field template
     *
     * @var string
     */
    private $defaultFieldTpl = '<tr><td>{LABEL}{REQUIRED}</td><td><div class="fieldError">{ERROR}</div>{FIELD}<div class="fieldDescription">{DESCRIPTION}</div></td></tr>';

    /**
     * Required field identifier
     *
     * @var string
     */
    private $requiredId = '<span style="color:#AA0000;">*</span>';

    /**
     * The fields
     *
     * @var array $fields
     */
    private $fields = array();

    /**
     * Whether or not to have a submission token
     *
     * @var boolean $submissionToken
     */
    private $submissionToken = true;

    /**
     * Sets up the form
     *
     * @param  string $name
     * @param  string $method=''
     * @param  string $action=''
     */
    public function __construct($name, $method='', $action='') {
        $this->name   = $name;
        $this->method = $method ?: 'post';
        $this->action = $action ?: Router::getUri();
    }

    /**
     * Sets the name of the form
     *
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Get the name of the field
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the method of the form
     *
     * @param string $method
     */
    public function setMethod($name) {
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
    public function setAction($name) {
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
     *
     * @return null
     */
    public function allowFileUploads() {
        $this->encType = 'multipart/form-data';
    }

    /**
     * Sets the form container
     *
     * @param  mixed $container
     * @return null
     */
    public function setContainer($container) {
        $this->formContainer = $container;
    }

    /**
     * Changes the required field marker
     *
     * @param  string $requiredId
     * @return null
     */
    public function setRequiredMarker($requiredId) {
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
     * Adds a field to the form
     *
     * @param  string  $type
     * @return mixed
     */
    public function addField($type, $name) {
        $class = '\Maverick\Lib\Builder_FormField_' . $type;

        if(array_key_exists($name, $this->fields)) {
            throw new \Exception('You cannot add multiple fields with the same name to the same form.');
        }

        $fieldBuilder = new $class($name);
        $fieldBuilder->value($this->getModel()->get($name))
            ->setNamespace($this->name);

        $this->fields[$name] = $fieldBuilder;

        return $fieldBuilder;
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
     * Renders the form for the controller
     *
     * @throws \Exception
     * @return string
     */
    public function render() {
        if(!count($this->fields)) {
            throw new \Exception('You didn\'t add any fields to the form');
        }

        if($this->name) {
            $this->addAntiCSRFToken();
        }

        $formContent  = '';
        $hiddenFields = '';

        foreach($this->fields as $name => $f) {
            if(!$f->isHidden()) {
                $tpl = $f->getTpl() ?: $this->defaultFieldTpl;
    
                $placeholders = array('{LABEL}'       => $f->getLabel(),
                                      '{REQUIRED}'    => $this->getRequiredMarker($f->isRequired()),
                                      '{ERROR}'       => $this->getFieldError($name),
                                      '{FIELD}'       => $f->render($this->name),
                                      '{DESCRIPTION}' => $f->getDescription());
    
                $formContent .= str_replace(array_keys($placeholders), array_values($placeholders), $tpl);
            } else {
                $hiddenFields .= $f->render($this->name);
            }
        }

        $form = new Builder_Tag('form');
        $form->addAttributes(array('name'     => $this->name,
                                   'method'   => $this->method,
                                   'action'   => $this->action));

        if($this->encType) {
            $form->addAttribute('enc-type', $this->encType);
        }

        $container = $this->formContainer;

        if(is_null($this->formContainer)) {
            $container = new Builder_Tag('table');
            $container->addAttribute('width', '100%');
        }

        $container->addContent($formContent);

        $form->addContent($container->render() . $hiddenFields);

        return $form->render();
    }

    /**
     * Adds an ID field to help prevent CSRF
     */
    private function addAntiCSRFToken() {
        if(!$this->submissionToken) {
            return false;
        }

        $token = $this->input->get('formSubmissionToken') ?: \Maverick\Lib\Utility::generateToken(25);

        $_SESSION[$this->name . '_submission_token'] = $token;

        $this->addField('Input_Hidden', 'formSubmissionToken')
            ->value($token);
    }

    /**
     * Toggle the submission token feature
     */
    public function toggleSubmissionToken() {
        $this->submissionToken = $this->submissionToken ? false : true;
    }

    /**
     * Says whether or not the submission token feature is enabled
     */
    public function submissionTokenEnabled() {
        return $this->submissionToken;
    }
}