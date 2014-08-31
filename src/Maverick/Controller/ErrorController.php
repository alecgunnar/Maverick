<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Controller;

use Maverick\Application,
    Maverick\View\ErrorView,
    Exception;

class ErrorController {
    private $debug;
    private $code;
    private $exception;

    public function __construct() {
        $this->debug = Application::debugCompare('<', Application::DEBUG_LEVEL_BETA);
    }

    public function setException(Exception $exception) {
        $this->exception = $exception;
        return $this;
    }

    public function delegateError($code) {
        $this->code = $code;
        $method     = 'show' . $code . 'ErrorAction';

        if(method_exists($this, $method)) {
            return $this->$method();
        }

        if($code >= 400 && $code <= 417) {
            return $this->clientErrorAction();
        } elseif($code >= 500 && $code <= 505) {
            return $this->serverErrorAction();
        }
    }

    public function clientErrorAction() {
        return ErrorView::renderGeneralError($this->code, $this->debug, $this->exception);
    }

    public function serverErrorAction() {
        return ErrorView::renderGeneralError($this->code, $this->debug, $this->exception);
    }

    public function show404ErrorAction() {
        return ErrorView::render404Error($this->debug, $this->exception);
    }
}