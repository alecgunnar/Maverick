<?php

namespace Application\Controller;

use \Maverick\Lib\Output;

class Headers extends \Maverick\Lib\Controller {
    public function main() {
        Output::setPageTitle('Headers');

        \Maverick\Lib\Http::location('/', '123');
    }
}