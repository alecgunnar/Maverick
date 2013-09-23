<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Output {
    /**
     * This holds and intance of the output class
     *
     * @var \Maverick\Output | null $instance
     */
    private static $instance = null;

    /**
     * Holds the instance of the choosen templating engine
     *
     * @var mixed $tplEngInst
     */
    private static $tplEngInst = null;

    /**
     * The current page's title
     *
     * @var string $pageTitle
     */
    private static $pageTitle = '';

    /**
     * The layout for the page
     *
     * @var string $layout
     */
    private static $pageLayout = 'Default';

    /**
     * Holds an array of all of the CSS files to be added to the page
     *
     * @var array $cssFiles
     */
    private static $cssFiles = array();

    /**
     * Holds an array of all of the Java Script files to be added to the page
     *
     * @var array $cssFiles
     */
    private static $jsFiles = array();

    /**
     * Global variables
     *
     * @var array $globalVariables
     */
    private static $globalVariables = array();

    /**
     * The constructor
     *
     * @return null
     */
    public static function initialize() {
        self::getTplEngine();

        self::$pageTitle = \Maverick\Maverick::getConfig('system')->get('site_name');
        self::setGlobalVariable('pageTitle', self::$pageTitle);
    }

    /**
     * Gets the instance of the template engine
     *
     * @return null
     */
    public static function getTplEngine() {
        if(is_null(self::$tplEngInst)) {
            $handler = 'Maverick\Lib\Output_Loader_' . \Maverick\Maverick::getConfig('output')->get('engine');

            self::$tplEngInst = new $handler;
        }

        return self::$tplEngInst;
    }

    /**
     * Sets the page layout
     *
     * @param  string $layout
     * @return null
     */
    public static function setPageLayout($layout) {
        $this->pageLayout = $layout;
    }

    /**
     * Returns the page layout
     *
     * @return string
     */
    public static function getPageLayout() {
        return self::$pageLayout;
    }

    /**
     * Sets the page title
     *
     * @param  string $pageTitle
     * @return null
     */
    public static function setPageTitle($pageTitle) {
        self::$pageTitle = $pageTitle;

        self::setGlobalVariable('pageTitle', $pageTitle);
    }

    /**
     * Returns the page title
     *
     * @return string
     */
    public static function getPageTitle() {
        return self::$pageTitle;
    }

    /**
     * Sets a global tpl variable
     *
     * @param string $name
     * @param mixed  $value
     */
    public static function setGlobalVariable($name, $value) {
        self::$globalVariables[$name] = $value;
    }

    /**
     * Sets multiple global variables
     *
     * @param array $variables
     */
    public static function setGlobalVariables($variables) {
        if(is_array($variables) && count($variables)) {
            foreach($variables as $k => $l) {
                self::setGlobalVariable($k, $l);
            }
        } else {
            throw new \Exception('An empty array or a string was set to ' . __NAMESPACE__
                               . '\Controller::setVariables(), an array with at least '
                               . 'one index is required.');
        }
    }

    /**
     * Returns all of the global variables
     *
     * @return array
     */
    public static function getGlobalVariables() {
        return self::$globalVariables;
    }

    /**
     * Adds a CSS file to the page
     *
     * @param  string $fileName
     * @return null
     */
    public static function addCssFile($fileName) {
        if(!preg_match('~^http~i', $fileName)) {
            self::$cssFiles[] = '/' . \Maverick\Maverick::getConfig('paths')->get('public')->get('css') . $fileName . '.css';
        } else {
            self::$cssFiles[] = $fileName;
        }
    }

    /**
     * Gets all of the CSS files to be added
     *
     * @return array
     */
    public static function getCssFiles() {
        return self::$cssFiles;
    }

    /**
     * Adds a JavaScript file to the page
     *
     * @param  string $fileName
     * @return null
     */
    public static function addJsFile($fileName) {
        if(!preg_match('~^http~i', $fileName)) {
            self::$jsFiles[] = '/' . \Maverick\Maverick::getConfig('paths')->get('public')->get('js') . $fileName . '.js';
        } else {
            self::$jsFiles[] = $fileName;
        }
    }

    /**
     * Gets all of the JavaScript files to be added
     *
     * @return array
     */
    public static function getJsFiles() {
        return self::$jsFiles;
    }

    /**
     * Outputs the page
     *
     * @param  array $variables=array()
     * @return null
     */
    public static function printOut($variables=array()) {
        $controller  = \Maverick\Lib\Router::getController(true);

        $checkForPageFiles = array('css', 'js');

        foreach($checkForPageFiles as $type) {
            if(\Maverick\Maverick::getConfig('Output')->get('auto_add_page_' . $type)) {
                $checkIn  = PUBLIC_PATH . \Maverick\Maverick::getConfig('paths')->get('public')->get($type);
                $file     = 'pages' . DS . strtolower(str_replace(array('\\', '_'), array('/', '-'), $controller));
    
                if(file_exists($checkIn . $file . '.' . $type)) {
                    $method = 'add' . ucfirst($type) . 'File';
    
                    self::$method($file);
                }
            }
        }

        self::$tplEngInst->printOut($variables);

        exit;
    }
}