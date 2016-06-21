<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router\Collection;

use Maverick\Router\Entity\RouteEntityInterface;

class RouteCollection implements RouteCollectionInterface
{
    /**
     * @var RouteEntityInterface[]
     */
    protected $routes = [];

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @inheritDoc
     */
    public function withRoute(RouteEntityInterface $route, string $name = null): RouteCollectionInterface
    {
        if ($name) {
            $this->routes[$name] = $route;
        } else {
            $this->routes[] = $route;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @inheritDoc
     */
    public function getRoute(string $name)
    {
        return isset($this->routes[$name]) ? $this->routes[$name] : null;
    }

    /**
     * @inheritDoc
     */
    public function mergeCollection(RouteCollectionInterface $collection): RouteCollectionInterface
    {
        $this->routes = array_merge($this->routes, $collection->getRoutes());

        return $this;
    }
}