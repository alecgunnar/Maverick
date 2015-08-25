<?php
/**
 * Maverick
 *
 * @package Maverick
 * @author  Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Controller;

use Maverick\Http\StandardRequest;

interface ControllerInterface
{
    public function doAction(StandardRequest $request);
}