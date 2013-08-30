<?php

/**
 * This config determines which template engine to use
 */

return array('twig'      => array('path_to_templates' => 'Application/Templates', // From the ROOT_PATH
                                  'environment'       => array('cache'      => false,
                                                               'autoescape' => 'html')));
