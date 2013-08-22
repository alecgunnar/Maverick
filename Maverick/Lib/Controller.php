<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Controller {
    /**
     * The instance of the output class
     *
     * @var \Maverick\Output | null $output
     */
    protected $output = null;

    /**
     * The tpl engine
     *
     * @var mixed $tpl
     */
    protected $tpl = null;

    /**
     * The tpl variables
     *
     * @var array $variables
     */
    private $variables = array('content' => '');

    /**
     * Should this controller be auto-outputted?
     * Usually, only layout controllers wont be.
     *
     * @var boolean $autoOutput
     */
    private $autoOutput = true;

    /**
     * Gets some things set up for the controller
     *
     * @return null
     */
    public function __construct() {
        $this->output = Output::getInstance();
        $this->tpl    = $this->output->getTplEngine();
    }

    /**
     * Sets the page title
     *
     * @param  string $pageTitle
     * @return null
     */
    public function setPageTitle($pageTitle) {
        $this->output->setPageTitle($pageTitle);
    }

    /**
     * Set multiple variables from an array
     *
     * @throws \Exception
     * @param  array $variables
     * @return null
     */
    public function setVariables($variables) {
        if(is_array($variables) && count($variables)) {
            foreach($variables as $k => $l) {
                $this->setVariable($k, $l);
            }
        } else {
            throw new \Exception('An empty array or a string was set to ' . __NAMESPACE__
                               . '\Controller::setVariables(), an array with at least '
                               . 'one index is required.');
        }
    }

    /**
     * Sets a variable
     *
     * @param  string $key
     * @param  mixed  $value
     * @return null
     */
    protected function setVariable($key, $value) {
        $this->variables[$key] = $value;
    }

    /**
     * Gets all of the tpl variables
     *
     * @return array
     */
    public function getVariables() {
        return $this->variables;
    }

    /**
     * Disable the auto-output function for the controller
     *
     * @return null
     */
    public function disableAutoOutput() {
        $this->autoOutput = false;
    }

    /**
     * Let the "outputter" take over, and print the page
     *
     * @return null
     */
    public function printOut() {
        if($this->autoOutput) {
            Output::getInstance()->printOut($this->variables);
        }
    }

    /**
     * Allows the controller output itself
     *
     * @return null
     */
    protected function printSelf() {
        Output::getInstance()->printOut($this->variables);
    }
}