<?php
/**
 * Maverick Container
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router;

use Psr\Http\Message\ServerRequestInterface;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std as RouteParser;
use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use Maverick\Middleware\Queue\MiddlewareQueueInterface;
use Maverick\Router\Collection\RouteCollectionInterface;
use Maverick\Router\Entity\RouteEntityInterface;

class FastRouteRouter extends AbstractRouter
{
    /**
     * @var RouteCollectionInterface
     */
    protected $collection;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @param RouteCollectionInterface $collection
     * @param Dispatcher $dispatcher
     */
    public function __construct(RouteCollectionInterface $collection, Dispatcher $dispatcher = null)
    {
        $this->collection = $collection;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param ServerRequestInterface $request
     * @return callable
     */
    public function handleRequest(ServerRequestInterface $request): callable
    {
        $results = $this->getDispatcher()
            ->dispatch($request->getMethod(), $request->getUri()->getPath());

        switch ($results[0]) {
            case Dispatcher::NOT_FOUND:
                return $this->notFoundHandler;

            case Dispatcher::METHOD_NOT_ALLOWED:
                return $this->notAllowedHandler;
        }

        return $results[1];
    }

    /**
     * @return Dispatcher
     */
    protected function getDispatcher()
    {
        if (!$this->dispatcher) {
            $collector = new RouteCollector(new RouteParser(), new DataGenerator());

            foreach ($this->collection as $route) {
                $collector->addRoute($route->getMethods(), $route->getPath(), $route);
            }

            $this->dispatcher = new GroupDispatcher($collector->getData());
        }

        return $this->dispatcher;
    }
}
