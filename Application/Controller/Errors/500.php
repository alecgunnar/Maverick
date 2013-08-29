<?php

namespace Application\Controller;

use \Maverick\Lib\Output;

class Errors_500 extends \Maverick\Lib\Controller {
    public function main() {
        Output::setPageTitle('There was an Error!');
    }
}