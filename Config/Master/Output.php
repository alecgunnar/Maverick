<?php

/**
 * This config determines which template engine to use
 */

$config['output']['engine']    = '\Maverick\Lib\Output_Twig';
$config['output']['templates'] = array('extension' => '.tpl');
$config['output']['twig']      = array('path_to_templates' => 'Application/Templates', // From the ROOT_PATH
                                       'environment'       => array(/*'cache' => ROOT_PATH . 'Application/Templates/Cache',*/
                                                                    'autoescape' => 'html'));

return array('engine'    => '\Maverick\Lib\Output_Twig',
             'templates' => array('extension' => '.tpl'),
             'twig'      => array('path_to_templates' => 'Application/Templates', // From the ROOT_PATH
                                  'environment'       => array('cache'      => ROOT_PATH . 'Application/Templates/Cache',
                                                               'autoescape' => 'html')));