<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router\Loader;

use Maverick\Router\Collection\RouteCollectionInterface;
use Maverick\Router\Entity\RouteEntity;

class FileSystemRouteLoader implements RouteLoaderInterface
{
    /**
     * @param string $location
     */
    protected $location;

    /**
     * @param string $location
     */
    public function __construct(string $location)
    {
        $this->location = $location;
    }

    /**
     * @inheritDoc
     */
    public function loadRoutes(RouteCollectionInterface $collection)
    {
        $routes = include($this->location);

        foreach ($routes as $name => $route) {
            $collection->withRoute(
                new RouteEntity(
                    isset($route['methods']) ? (array) $route['methods'] : ['GET'],
                    $route['path'],
                    $route['handler']
                ), $name
            );
        }
    }
}
