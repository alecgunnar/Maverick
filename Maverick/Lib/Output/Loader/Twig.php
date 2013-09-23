<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Output_Loader_Twig extends \Maverick\Lib\Output_Loader {
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
     */
    public function __construct() {
        $config = \Maverick\Maverick::getConfig('output')->get('twig');

        $this->loader = new \Twig_Loader_Filesystem(ROOT_PATH . $config->get('path_to_templates'));
        $this->twig   = new \Twig_Environment($this->loader, $config->getAsArray('environment'));
    }

    /**
     * Gets a template
     *
     * @param  string $tplName
     * @param  array  $variables=array()
     * @return string
     */
    public function getTemplate($tplName, $variables=array()) {
        $tpl = $this->twig->loadTemplate($tplName . \Maverick\Maverick::getConfig('output')->get('templates')->get('extension'));

        return $tpl->render(array_merge(Output::getGlobalVariables(), $variables));
    }

    /**
     * Outputs the page
     *
     * @param  array $variables=array()
     */
    public function printOut($variables=array()) {
        $expClass  = explode('\\', get_class(Router::getController()));
        $view      = str_replace('_', '/', array_pop($expClass));

        try {
            $variables['content'] = $this->getTemplate('Views/' . $view, $variables);
        } catch(\Twig_Error_Loader $e) {
            // If there isn't a view specific for this page, just send everything to the layout
        }

        print $this->getTemplate('Layouts/Base', array('title'    => Output::getPageTitle(),
                                                       'cssFiles' => Output::getCssFiles(),
                                                       'jsFiles'  => Output::getJsFiles(),
                                                       'body'     => $this->getLayout($variables)));

        exit;
    }
}