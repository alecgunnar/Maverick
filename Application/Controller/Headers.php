<?php

namespace Application\Controller;

use \Maverick\Lib\Output;

class Headers extends \Maverick\Lib\Controller {
    public function main() {
        Output::setPageTitle('Headers');

        $db = new \Maverick\Lib\DataSource_MySql;

//        \Maverick\Lib\Http::location('/', '123');
    }
}