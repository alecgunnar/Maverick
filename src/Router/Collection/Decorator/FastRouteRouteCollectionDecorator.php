<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */
declare(strict_types=1);

namespace Maverick\Router\Collection\Decorator;

use FastRoute\RouteCollector;
use Maverick\Router\Collection\RouteCollectionInterface;
use Maverick\Router\Entity\RouteEntityInterface;

class FastRouteRouteCollectionDecorator extends RouteCollectionDecorator
{
    /**
     * @param RouteCollector $collector
     */
    public function __invoke(RouteCollector $collector)
    {
        $routes = $this->collection->getRoutes();

        foreach ($routes as $route) {
            $collector->addRoute(
                $route->getMethods(),
                $route->getPath(),
                $route
            );
        }
    }
}
