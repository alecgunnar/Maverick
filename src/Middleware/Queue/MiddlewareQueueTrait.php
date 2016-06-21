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
    public function withMiddleware(callable $handler): MiddlewareQueueInterface
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
    public function run(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if ($this->middleware) {
            $handler  = array_shift($this->middleware);
            $response = $handler($request, $response, [$this, 'run']);

            if (!($response instanceof ResponseInterface)) {
                throw new InvalidMiddlewareException('Middleware did not return an instance of ' . ResponseInterface::class . '.');
            }
        }

        return $response;
    }
}