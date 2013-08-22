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
    public $name = '';

    /**
     * The method of the form
     *
     * @var string $method
     */
    public $method = 'post';

    /**
     * The action of the form
     *
     * @var string $action
     */
    public $action = '';

    /**
     * The encoding type of the form
     *
     * @var string | null
     */
    public $encType = null;

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
    protected $fields = array();

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
     * Changes the required field identifier
     *
     * @param  string $requiredId
     * @return null
     */
    public function setRequiredIdentifier($requiredId) {
        $this->requiredId = $requiredId;
    }

    /**
     * Gets the required field identifier if needed
     *
     * @param  boolean $isRequired=true
     * @return string
     */
    public function getRequiredIdentifier($isRequired=true) {
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
        $fieldBuilder->value($this->getModel()->get($name));

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
            throw new \Exception('You didn\'t add any fields to the form!');
        }

        $formContent = '';

        foreach($this->fields as $name => $f) {
            $tpl = $f->tpl ?: $this->defaultFieldTpl;

            $placeholders = array('{LABEL}'       => $f->label,
                                  '{REQUIRED}'    => $this->getRequiredIdentifier($f->required),
                                  '{ERROR}'       => $this->getFieldError($name),
                                  '{FIELD}'       => $f->render(),
                                  '{DESCRIPTION}' => $f->description);

            $formContent .= str_replace(array_keys($placeholders), array_values($placeholders), $tpl);
        }

        $form = new Builder_Tag('form');
        $form->addAttributes(array('name'     => $this->name,
                                   'method'   => $this->method,
                                   'action'   => $this->action,
                                   'enc-type' => $this->encType));

        $container = $this->formContainer;

        if(is_null($this->formContainer)) {
            $container = new Builder_Tag('table');
            $container->addAttribute('width', '100%');
        }

        $container->addContent($formContent);

        $form->addContent($container->render());

        return $form->render();
    }
}