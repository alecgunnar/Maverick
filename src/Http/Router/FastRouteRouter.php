<?php

namespace Maverick\Http\Router;

use Psr\Http\Message\ServerRequestInterface;
use Maverick\Http\Router\Route\Collection\CollectionInterface;
use Maverick\Http\Router\Route\Route;
use FastRoute\Dispatcher;

class FastRouteRouter implements RouterInterface
{
    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @var CollectionInterface
     */
    protected $collection;

    /**
     * @var Route
     */
    protected $route;

    /**
     * @var string[]
     */
    protected $vars;

    /**
     * @var string[]
     */
    protected $allowed;

    /**
     * @param Dispatcher $dispatcher
     * @param CollectionInterface $collection
     */
    public function __construct(Dispatcher $dispatcher, CollectionInterface $collection)
    {
        $this->dispatcher = $dispatcher;
        $this->collection = $collection;
    }

    public function processRequest(ServerRequestInterface $request): int
    {
        $ret = $this->dispatcher->dispatch(
            $request->getMethod(),
            $request->getUri()->getPath()
        );

        switch ($ret[0]) {
            case Dispatcher::FOUND:
                $this->route = $this->collection->getRoute($ret[1]);
                $this->vars = $ret[2];
                return RouterInterface::STATUS_FOUND;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $this->allowed = $ret[1];
                return RouterInterface::STATUS_NOT_ALLOWED;
            default:
                return RouterInterface::STATUS_NOT_FOUND;
        }
    }

    public function getRoute(): Route
    {
        return $this->route;
    }

    public function getAllowedMethods(): array
    {
        return $this->allowed;
    }

    public function getUriVars(): array
    {
        return $this->vars;
    }
}
