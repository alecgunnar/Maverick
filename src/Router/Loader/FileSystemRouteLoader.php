<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router\Loader;

use Maverick\Router\Collection\RouteCollectionInterface;
use Maverick\Router\Entity\RouteEntity;

class FileSystemRouteLoader extends AbstractRouteLoader
{
    /**
     * @param string $location
     */
    protected $location;

    /**
     * @param RouteCollectionInterface $collection
     */
    public function __construct(RouteCollectionInterface $collection, string $location)
    {
        $this->collection = $collection;
        $this->location   = $location;
    }

    public function loadRoutes(): RouteCollectionInterface
    {
        $routes = include($this->location);

        foreach ($routes as $name => $route) {
            $this->collection->withRoute(
                new RouteEntity(
                    $route['methods'] ?? ['GET'],
                    $route['path'],
                    $route['handler']
                ), $name
            );
        }

        return $this->collection;
    }
}
