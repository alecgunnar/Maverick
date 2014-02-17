<?php

namespace Application\Controller;

use \Maverick\Lib\Output;

class Search extends \Maverick\Lib\Controller {
    public function main($what='') {
        dump(func_get_args());
    }
}