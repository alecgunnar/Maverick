<?php

namespace Application\Controller;

use \Maverick\Lib\Output;

class Foo_Index extends \Maverick\Lib\Controller {
    public function main() {
        Output::setPageTitle('Foo');
    }
}