<?php
/**
 * Maverick
 *
 * @package Maverick
 * @author  Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Controller;

use Maverick\Http\StandardRequest;
use Maverick\Http\StandardResponse;

class StandardController implements ControllerInterface
{
    protected $response;

    public function __construct(StandardResponse $response)
    {
        $this->response = $response;
    }

    protected function render($view, $params=[])
    {
        return $this->response->setView($view, $params);
    }

    protected function redirectUri($uri, $flash='', $status=303)
    {
        $this->response = new RedirectResponse($uri, $status);

        return $this->response;
    }

    protected function redirectNamed($name, $flash='', $status=303)
    {
        return $this->response;
    }

    public function doAction(StandardRequest $request)
    {
        if ($view = $request->attributes->get('view')) {
            $this->render($view, $request->attributes->all());
        }
    }
}