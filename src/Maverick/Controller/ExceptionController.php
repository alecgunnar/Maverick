<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Controller;

use Maverick\Application,
    Maverick\View\ExceptionView,
    Exception;

class ExceptionController {
    private $debug;

    public function __construct(Application $app) {
        $this->debug = $app->debugCompare('<', Application::DEBUG_LEVEL_BETA);
    }

    public function error500Action(Exception $e) {
        return ExceptionView::render500($e, $this->debug);
    }

    public function error404Action(Exception $e) {
        return ExceptionView::render404($e->getMessage(), $this->debug);
    }
}