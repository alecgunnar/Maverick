<?php

namespace Maverick\Testing\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class SampleMiddleware
{
    public function __invoke(ServerRequestInterface $req, ResponseInterface $resp, callable $next)
    {

    }
}