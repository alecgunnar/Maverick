<?php

namespace Maverick\Http\Exception;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

interface HttpExceptionInterface
{
    /**
     * @return ServerRequestInterface
     */
    public function getServerRequest(): ServerRequestInterface;

    /**
     * @return int
     */
    public function getStatusCode(): int;
}
