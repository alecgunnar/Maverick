<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Model_Session extends Model {
    /**
     * The data  templatefor this model
     *
     * @var array $dataTemplate
     */
    private $dataTemplate = array('user'    => '',
                                  'cookies' => '');

    /**
     * The constructor
     *
     * @param  array $data=array()
     * @return null
     */
    public function __construct($data=array()) {
        $template = array_merge($this->dataTemplate, $data);

        parent::__construct($template);
    }
}