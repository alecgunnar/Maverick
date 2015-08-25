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

    public function doAction(StandardRequest $request)
    {

    }    
}