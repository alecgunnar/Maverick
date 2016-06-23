<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Middleware\Queue;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Maverick\Middleware\Exception\InvalidMiddlewareException;

interface MiddlewareQueueInterface
{
    /**
     * @param callable $handler
     * @return MiddlewareQueueInterface
     */
    public function withMiddleware(callable $handler): MiddlewareQueueInterface;

    /**
     * @param callable[] $handler
     * @return MiddlewareQueueInterface
     */
    public function withMiddlewares(array $handlers): MiddlewareQueueInterface;

    /**
     * @return callable[]
     */
    public function getMiddleware(): array;

    /**
     * @throws InvalidMiddlewareException If a middleware does not return a response
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function run(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;
}