<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Controller;

use Maverick\Http\Response,
    Maverick\View\ExceptionView,
    Exception;

class ExceptionController {
    /**
     * The current response object
     *
     * @var Maverick\Http\Response
     */
    protected $response;

    /**
     * Construtor
     *
     * @param Maverick\Http\Response $response
     */
    public function __construct(Response $response) {
        $this->response = $response;
    }

    public function showErrorAction(Exception $e) {
        $code = 500;

        if(get_class($e) == 'Maverick\Exception\NoRouteException') {
            $code = 404;
        }

        $this->response->setStatus($code);

        return ExceptionView::render($code, $e);
    }
}