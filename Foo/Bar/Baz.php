<?php

namespace Application\Controller;

use \Maverick\Lib\Output;

class Foo_Bar_Baz extends \Maverick\Lib\Controller {
    public function main() {
        Output::setPageTitle('Bazz');
    }
}