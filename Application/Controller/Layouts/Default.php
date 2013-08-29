<?php

namespace Application\Controller;

use \Maverick\Lib\Output;

class Layouts_Default extends \Maverick\Lib\Controller {
    public function main($variables) {
        $this->setVariables($variables);
    }
}