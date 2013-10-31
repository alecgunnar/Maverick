<?php

namespace Application\Controller;

use \Maverick\Lib\Output;

class Foo_Bar_BazzInga extends \Maverick\Lib\Controller {
    public function main() {
        Output::setPageTitle('Baz');
    }
}