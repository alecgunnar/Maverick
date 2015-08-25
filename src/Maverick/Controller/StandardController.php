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
    protected $request;
    protected $response;

    public function __construct(StandardRequest $request, StandardResponse $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }

    public function doAction()
    {

    }    
}