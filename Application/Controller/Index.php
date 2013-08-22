<?php

namespace Application\Controller;

class Index extends \Maverick\Lib\Controller {
    public function main() {
        $this->setPageTitle('Welcome to Maverick');
        $this->setVariable('pathToController', __FILE__);
    }
}