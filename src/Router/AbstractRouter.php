<?php
/**
 * Maverick Container
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */
declare(strict_types=1);

namespace Maverick\Router;

use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractRouter
{
    /**
     * @var callable
     */
    protected $notFoundHandler;

    /**
     * @var callable
     */
    protected $notAllowedHandler;

    /**
     * @var mixed[]
     */
    protected $params = [];

    /**
     * @param callable $handler
     */
    public function setNotFoundHandler(callable $handler): AbstractRouter
    {
        $this->notFoundHandler = $handler;
        return $this;
    }

    /**
     * @param callable $handler
     */
    public function setNotAllowedHandler(callable $handler): AbstractRouter
    {
        $this->notAllowedHandler = $handler;
        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getParams(): array
    {
        return (array) $this->params;
    }

    /**
     * @param ServerRequestInterface $request
     * @return callable
     */
    abstract public function handleRequest(ServerRequestInterface $request): callable;
}
