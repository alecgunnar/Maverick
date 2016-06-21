<?php
/**
 * Maverick Container
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router\Collection;

use Maverick\Router\Entity\RouteEntityInterface;

interface RouteCollectionInterface
{
    /**
     * Add the given route (with the optional name) to the collection.
     *
     * If the name is provided, the association should be retained.
     *
     * @param RouteEntityInterface $route
     * @param string $name = null
     * @return RouteCollectionInterface
     */
    public function withRoute(RouteEntityInterface $route, string $name = null): RouteCollectionInterface;

    /**
     * Return the list of routes
     *
     * @return RouteEntityInterface[]
     */
    public function getRoutes(): array;

    /**
     * Get a route by name
     *
     * @param string $name
     * @return RouteEntityInterface
     */
    public function getRoute(string $name): RouteEntityInterface;

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
