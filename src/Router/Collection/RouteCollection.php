<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */
declare(strict_types=1);

namespace Maverick\Router\Collection;

use Maverick\Router\Entity\RouteEntityInterface;

class RouteCollection implements RouteCollectionInterface
{
    /**
     * @var RouteEntityInterface[]
     */
    protected $routes = [];

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @inheritDoc
     */
    public function withRoute(RouteEntityInterface $route, string $name = null): RouteCollectionInterface
    {
        if ($this->prefix) {
            $route->withPrefix($this->prefix);
        }

        if ($name) {
            $this->routes[$name] = $route;
        } else {
            $this->routes[] = $route;
        }

        return $this;
    }

    /**
     * @param RouteEntityInterface[] $route
     * @return RouteCollectionInterface
     */
    public function withRoutes(array $routes): RouteCollectionInterface
    {
        foreach ($routes as $name => $route) {
            $this->withRoute($route, is_string($name) ? $name : null);
        }

        return $this;
    }

    /**
     * Prefix all routes in the collection with this
     *
     * @param string $prefix
     * @return RouteCollectionInterface
     */
    public function setPrefix(string $prefix): RouteCollectionInterface
    {
        $this->prefix = $prefix;
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
        if (isset($this->routes[$name])) {
            return $this->routes[$name];
        }

        return null;
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
