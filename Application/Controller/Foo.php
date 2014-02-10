<?php

namespace Application\Controller;

use \Maverick\Lib\Output;

class Foo extends \Maverick\Lib\Controller {
    public static function rootSetup() {
        Output::setPageTitle('This is Foo!');
    }

    public function main() {
        Output::setPageTitle('Foo');

        Output::printJson(array('a' => 'b'));
    }
}