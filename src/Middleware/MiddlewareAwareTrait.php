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

trait MiddlewareAwareTrait
{
    /**
     * @var callable[]
     */
    protected $middleware = [];

    /**
     * @param callable $handler
     * @return self
     */
    public function withMiddleware(callable $handler): MiddlewareAwareInterface
    {
        $this->middleware[] = $handler;
        return $this;
    }

    /**
     * @return callable[]
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * @throws InvalidMiddlewareException
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        static $index = 0;

        if (count($this->middleware) > $index) {
            $handler = $this->middleware[$index++];
            $response = $handler($request, $response, [$this, 'dispatch']);

            if (!($response instanceof ResponseInterface)) {
                throw new InvalidMiddlewareException('Middleware did not return instance of Psr\Http\Message\ResponseInterface.');
            }
        }

        return $response;
    }
}