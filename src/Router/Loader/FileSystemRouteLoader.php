<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router\Loader;

use Interop\Container\ContainerInterface;
use Maverick\Router\Collection\RouteCollectionInterface;
use Maverick\Router\Entity\RouteEntity;

class FileSystemRouteLoader implements RouteLoaderInterface
{
    /**
     * @var string
     */
    protected $location;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param string $location
     * @param ContainerInterface $container
     */
    public function __construct(string $location, ContainerInterface $container)
    {
        $this->location  = $location;
        $this->container = $container;
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
                    is_callable($route['handler']) ? $route['handler'] : $this->container->get((string) $route['handler'])
                ), $name
            );
        }
    }
}
