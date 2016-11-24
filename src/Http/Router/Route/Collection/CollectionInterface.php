<?php

namespace Maverick\Http\Router\Route\Collection;

use Maverick\Http\Router\Route\Route;
use Countable;

interface CollectionInterface extends Countable
{
    /**
     * Adds a new route to the collection
     *
     * @param string $name
     * @param Route $route
     */
    public function withRoute(string $name, Route $route);

    /**
     * Adds all of the routes to the collection
     *
     * @param CollectionInterface $routes
     */
    public function withRoutes(CollectionInterface $routes);

    /**
     * Returns an array of all of the routes
     *
     * @return Route[]
     */
    public function all(): array;

    /**
     * Get the route matching the given name
     *
     * @throws Exception
     * @return Route
     */
    public function getRoute(string $name): Route;
}
