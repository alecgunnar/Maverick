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

class FastRouteRouter extends AbstractRouter
{
    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param ServerRequestInterface $request
     * @return callable
     */
    public function handleRequest(ServerRequestInterface $request): callable
    {
        $results = $this->dispatcher->dispatch(
            $request->getMethod(),
            $request->getUri()->getPath()
        );

        switch ($results[0]) {
            case Dispatcher::NOT_FOUND:
                return $this->notFoundHandler;

            case Dispatcher::METHOD_NOT_ALLOWED:
                return $this->notAllowedHandler;

            case Dispatcher::FOUND:
                $this->params = $results[2];
        }

        return $results[1];
    }
}
