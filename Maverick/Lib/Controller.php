<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Controller {
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
    private $allowPrintOut = true;

    /**
     * Gets some things set up for the controller
     */
    public function __construct() {
        $this->tpl = \Maverick\Lib\Output::getTplEngine();
    }

    /**
     * Disable the auto-output function for the controller
     */
    public function disableAutoOutput() {
        $this->allowPrintOut = false;
    }

    /**
     * Set multiple variables from an array
     *
     * @throws \Exception
     * @param  array $variables
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
     * @param string $key
     * @param mixed  $value
     */
    public function setVariable($key, $value) {
        $this->variables[$key] = $value;
    }

    /**
     * Gets the variables for this controller
     *
     * @return array
     */
    public function getVariables() {
        return $this->variables;
    }

    /**
     * Prints out the controller
     *
     * @throws \Exception
     */
    public function printOut() {
        if($this->allowPrintOut) {
            \Maverick\Lib\Output::printOut($this->variables);
        } else {
            throw new \Exception('This controller cannot be printed!');
        }
    }
}