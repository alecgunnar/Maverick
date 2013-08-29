<?php

namespace Application\Controller;

use Maverick\Lib\Output;

class AppRoot extends \Maverick\Lib\Controller {
    public function main() {
        Output::addCssFile('main');
    }
}