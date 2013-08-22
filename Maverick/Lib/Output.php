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
    private $pageTitle = '';

    /**
     * The layout for the page
     *
     * @var string $layout
     */
    private $pageLayout = 'Default';

    /**
     * Holds an array of all of the CSS files to be added to the page
     *
     * @var array $cssFiles
     */
    private $cssFiles = array();

    /**
     * The constructor
     *
     * @return null
     */
    protected function __construct() { }

    /**
     * Gets the instance of the template engine
     *
     * @return null
     */
    public function getTplEngine() {
        if(is_null(self::$tplEngInst)) {
            $engine  = \Maverick\Maverick()->getConfig('output')->get('engine');
            $handler = 'Maverick\Lib\Output_';

            if(strpos($engine, __NAMESPACE__) === false) {
                $handler .= $engine;
            } else {
                $handler = $engine;
            }

            self::$tplEngInst = new $handler;
        }

        return self::$tplEngInst;
    }

    /**
     * Gets the instance of the class
     *
     * @return \Maverick\Output
     */
    public static function getInstance() {
        if(is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Sets the page layout
     *
     * @param  string $layout
     * @return null
     */
    public function setPageLayout($layout) {
        $this->pageLayout = $layout;
    }

    /**
     * Returns the page layout
     *
     * @return string
     */
    public function getPageLayout() {
        return $this->pageLayout;
    }

    /**
     * Sets the page title
     *
     * @param  string $pageTitle
     * @return null
     */
    public function setPageTitle($pageTitle) {
        $this->pageTitle = $pageTitle;
    }

    /**
     * Returns the page title
     *
     * @return string
     */
    public function getPageTitle() {
        return $this->pageTitle ?: \Maverick\Maverick()->getConfig('system')->get('site')->get('site_name');
    }

    /**
     * Adds a CSS file to the page
     *
     * @param  string $fileName
     * @return null
     */
    public function addCssFile($fileName) {
        $this->cssFiles[] = '/' . \Maverick\Maverick()->getConfig('paths')->get('public')->get('css') . $fileName . '.css';
    }

    /**
     * Gets all of the CSS files to be added
     *
     * @return array
     */
    public function getCssFiles() {
        return $this->cssFiles;
    }

    /**
     * Outputs the page
     *
     * @param  $content=''
     * @return null
     */
    public function printOut($content='') {
        $controller  = \Maverick\Lib\Router::getInstance()->getController(true);
        $pageCssFile = 'pages/' . strtolower(str_replace(array('\\', '_'), array('/', '-'), $controller));
        $checkIn     = PUBLIC_PATH . \Maverick\Maverick()->getConfig('paths')->get('public')->get('css');
        $checkPath   = $checkIn . $pageCssFile . '.css';

        if(file_exists($checkIn . $pageCssFile . '.css')) {
            $this->addCssFile($pageCssFile);
        }

        self::$tplEngInst->printOut($content);

        exit;
    }
}