<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */
declare(strict_types=1);

namespace Maverick\Middleware\Queue;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Maverick\Middleware\Exception\InvalidMiddlewareException;

trait MiddlewareQueueTrait
{
    /**
     * @var callable[]
     */
    protected $middleware = [];

    /**
     * @param callable $handler
     * @return self
     */
    public function with(callable $handler): MiddlewareQueueInterface
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
     * @inheritDoc
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null): ResponseInterface
    {
        if ($this->middleware) {
            $handler  = array_shift($this->middleware);
            $response = $handler($request, $response, $this);

            if (!($response instanceof ResponseInterface)) {
                throw new InvalidMiddlewareException('Middleware did not return an instance of ' . ResponseInterface::class . '.');
            }
        }

        return is_callable($next) ? $next($request, $response) : $response;
    }
}