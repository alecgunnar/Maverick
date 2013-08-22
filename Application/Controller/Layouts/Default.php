<?php

namespace Application\Controller;

class Layouts_Default extends \Maverick\Lib\Controller {
    /**
     * The main method
     *
     * @return null
     */
    public function main($variables) {
        $this->setVariables($variables);
    }
}