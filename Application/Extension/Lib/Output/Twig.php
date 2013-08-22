<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Output_Twig extends \Maverick\Lib\Output_Loader {
    /**
     * The template loader
     *
     * @var Twig_Loader_Filesystem | null $loader
     */
    public $loader = null;

    /**
     * The Twig environment
     *
     * @var Twig_Environment | null $twig
     */
    public $twig = null;

    /**
     * Sets up the engine
     *
     * @return null
     */
    public function __construct() {
        $config = \Maverick\Maverick()->getConfig('output')->get('twig');

        $this->loader = new \Twig_Loader_Filesystem(ROOT_PATH . $config->get('path_to_templates'));
        $this->twig   = new \Twig_Environment($this->loader, $config->getAsArray('environment'));
    }

    /**
     * Outputs the page
     *
     * @param  array $variables=array()
     * @return null
     */
    public function printOut($variables=array()) {
        $tplExt = \Maverick\Maverick()->getConfig('output')->get('templates')->get('extension');

        $output = Output::getInstance();

        $router    = \Maverick\Lib\Router::getInstance();
        $expClass  = explode('\\', get_class($router->getController()));
        $view      = str_replace('_', '/', array_pop($expClass));

        try {
            $viewTpl               = $this->twig->loadTemplate('Views/' . $view . $tplExt);
            $variables['content']  = $viewTpl->render($variables);
        } catch(\Twig_Error_Loader $e) {
            // If there isn't a view specific for this page, just send everything to the layout
        }

        $layout = $this->getLayout($variables);
        $base   = $this->twig->loadTemplate('Layouts/Base' . $tplExt);

        $pageTitle = Output::getInstance()->getPageTitle();

        $base->display(array('title'    => $pageTitle,
                             'cssFiles' => $output->getCssFiles(),
                             'body'     => $layout));

        exit;
    }

    /**
     * Gets the rendered page layout
     *
     * @param  array $variables
     * @return string
     */
    protected function getLayout($variables) {
        $layout     = ucfirst(Output::getInstance()->getPageLayout()) ?: 'Default';
        $class      = '\Application\Controller\Layouts_' . $layout;
        $controller = new $class;

        $controller->disableAutoOutput();
        $controller->main($variables);

        $layoutTpl = $this->twig->loadTemplate('Layouts/' . $layout . \Maverick\Maverick()->getConfig('output')->get('templates')->get('extension'));

        return $layoutTpl->render($controller->getVariables());
    }
}