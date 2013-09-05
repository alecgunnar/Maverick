<?php

/**
 * This config determines which template engine to use
 */

return array('auto_add_page_css' => true,
             'auto_add_page_js'  => true,
             'engine'            => '\Maverick\Lib\Output_Twig',
             'templates'         => array('extension' => '.tpl'),
             'twig'              => array('path_to_templates' => 'Application' . DS . 'Templates', // From the ROOT_PATH
                                          'environment'       => array('cache'      => ROOT_PATH . 'Application' . DS . 'Templates' . DS . 'Cache',
                                                                       'autoescape' => 'html')));
