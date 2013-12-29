<?php

namespace Application\Controller;

use \Maverick\Lib\Output;

class Foo extends \Maverick\Lib\Controller {
    public function main() {
        Output::setPageTitle('Foo');

        Output::printJson(array('a' => 'b'));
    }
}