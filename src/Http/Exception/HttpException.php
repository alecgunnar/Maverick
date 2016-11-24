<?php

namespace Maverick\Http\Exception;

use RuntimeException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class HttpException extends RuntimeException implements HttpExceptionInterface
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    public function __construct(string $message, ServerRequestInterface $request)
    {
        parent::__construct($message);

        $this->request = $request;
    }

    public function getServerRequest(): ServerRequestInterface
    {
        return $this->request;
    }
}
