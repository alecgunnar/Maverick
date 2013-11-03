<?php

namespace Application\Controller;

use Maverick\Lib\Output;

class AppRoot {
    public function preload() {
        Output::addCssFile('main');
    }

    public function postload() { }
}