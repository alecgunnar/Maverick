<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */
declare(strict_types=1);

namespace Maverick\Router;

use Psr\Http\Message\ServerRequestInterface;
use FastRoute\Dispatcher;
use FastRoute\RouteParser\Std as RouteParser;
use FastRoute\DataGenerator\GroupCountBased as DataGenerator;

class FastRouteRouter extends AbstractRouter
{
    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param ServerRequestInterface $request
     * @return int
     */
    public function checkRequest(ServerRequestInterface $request): int
    {
        $results = $this->dispatcher->dispatch(
            $request->getMethod(),
            $request->getUri()->getPath()
        );

        switch ($results[0]) {
            case Dispatcher::FOUND:
                $this->matched = $results[1];
                $this->params  = $results[2];
                return self::ROUTE_FOUND;

            case Dispatcher::METHOD_NOT_ALLOWED:
                $this->methods = $results[1];
                return self::ROUTE_NOT_ALLOWED;
        }

        return self::ROUTE_NOT_FOUND;
    }
}
