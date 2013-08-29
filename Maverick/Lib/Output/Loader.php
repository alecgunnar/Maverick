<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

abstract class Output_Loader {
    /**
     * Sets up the engine
     *
     * @return null
     */
    public abstract function __construct();

    /**
     * Gets a template
     *
     * @param  string $tplName
     * @param  array  $variables=array()
     * @return string
     */
    protected abstract function getTemplate($tplName, $variables=array());

    /**
     * Outputs the page
     *
     * @param  array $variables=array()
     * @return null
     */
    public abstract function printOut($variables=array());

    /**
     * Gets the rendered page layout
     *
     * @param  array $variables
     * @return string
     */
    protected function getLayout($variables) {
        $layout     = ucfirst(Output::getPageLayout()) ?: 'Default';
        $class      = '\Application\Controller\Layouts_' . $layout;
        $controller = new $class;

        $controller->disableAutoOutput();
        $controller->main($variables);

        $layoutTpl = 'Layouts/' . $layout;

        return $this->getTemplate($layoutTpl, $controller->getVariables());
    }
}