<?php
/**
 * Maverick Container
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router\Collection;

use Maverick\Router\Route\RouteInterface;

interface RouteCollectionInterface
{
    /**
     * Add the given route (with the optional name) to the collection.
     *
     * If the name is provided, the association should be retained.
     *
     * @param RouteInterface $route
     * @param string $name = null
     * @return RouteCollectionInterface
     */
    public function withRoute(RouteInterface $route, string $name = null): RouteCollectionInterface;

    /**
     * Get a route by name
     *
     * @param string $name
     * @return RouteInterface
     */
    public function getRoute(string $name): RouteInterface;

    /**
     * Take the routes in the given collection, then add them to the
     * current one.
     *
     * If a name conflict occurrs, the route from the given collection
     * should be kept.
     *
     * @param RouteCollectionInterface $collection
     * @param RouteCollectionInterface
     */
    public function mergeCollection(RouteCollectionInterface $collection): RouteCollectionInterface;
}
