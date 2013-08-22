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
    protected abstract function getLayout($variables);
}