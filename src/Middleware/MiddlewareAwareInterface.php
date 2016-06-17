<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */
declare(strict_types=1);

namespace Maverick\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Maverick\Middleware\Exception\InvalidMiddlewareException;

interface MiddlewareAwareInterface
{
    /**
     * @param callable $handler
     * @return MiddlewareAwareInterface
     */
    public function withMiddleware(callable $handler): MiddlewareAwareInterface;

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