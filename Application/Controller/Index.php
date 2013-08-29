<?php

namespace Application\Controller;

use \Maverick\Lib\Output;

class Index extends \Maverick\Lib\Controller {
    public function main() {
        Output::setPageTitle('Welcome to Maverick');
        $this->setVariable('pathToController', __FILE__);
    }
}