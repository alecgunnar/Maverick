<?php

namespace Application\Controller;

class Errors_404 extends \Maverick\Lib\Controller {
    public function main() {
        $this->setPageTitle('Page not found!');
    }
}