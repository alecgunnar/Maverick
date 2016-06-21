<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router\Loader;

use Maverick\Router\Collection\RouteCollectionInterface;
use Maverick\Router\Loader\RouteLoaderInterface;

class FileSystemRouteLoader implements RouteLoaderInterface
{
    /**
     * @var RouteLoaderInterface $loader
     */
    protected $loader;

    /**
     * @param string $location
     * @param RouteLoaderInterface $container
     */
    public function __construct(string $location, RouteLoaderInterface $loader)
    {
        $this->loader = $loader;

        $this->loader->withRoutes(include($location));
    }

    /**
     * @inheritDoc
     */
    public function loadRoutes(RouteCollectionInterface $collection)
    {
        $this->loader->loadRoutes($collection);
    }

    /**
     * @inheritDoc
     */
    public function withRoutes(array $routes): RouteLoaderInterface
    {
        $this->loader->withRoutes($routes);
        return $this;
    }
}
