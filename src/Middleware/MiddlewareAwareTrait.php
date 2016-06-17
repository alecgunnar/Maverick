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
    protected $queue = [];

    /**
     * @param callable $handler
     * @return self
     */
    public function enqueue(callable $handler) : self
    {
        $this->queue[] = $handler;
        
        return $this;
    }

    /**
     * @return callable[]
     */
    public function getQueue() : array
    {
        return $this->queue;
    }

    /**
     * @throws InvalidMiddlewareException
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        static $index = 0;

        if (count($this->queue) > $index) {
            $handler = $this->queue[$index++];
            $response = $handler($request, $response, [$this, 'dispatch']);

            if (!($response instanceof ResponseInterface)) {
                throw new InvalidMiddlewareException('Middleware did not return instance of Psr\Http\Message\ResponseInterface.');
            }
        }

        return $response;
    }
}