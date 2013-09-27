<?php

namespace Application\Controller;

use \Maverick\Lib\Output;

class Errors_404 extends \Maverick\Lib\Controller {
    public function main() {
        Output::setPageTitle('Page not found!');

        http_response_code(404);
    }
}