<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Middleware\Queue;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Maverick\Middleware\MiddlewareInterface;
use Maverick\Middleware\Exception\InvalidMiddlewareException;

interface MiddlewareQueueInterface extends MiddlewareInterface
{
    /**
     * @param callable $handler
     * @return MiddlewareQueueInterface
     */
    public function with(callable $handler): MiddlewareQueueInterface;

    /**
     * @return callable[]
     */
    public function getMiddleware(): array;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next = null
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface;
}