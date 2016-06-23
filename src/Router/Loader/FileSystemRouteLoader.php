<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router\Loader;

use Interop\Container\ContainerInterface;

class FileSystemRouteLoader extends RouteLoader
{
    /**
     * @param ContainerInterface $container
     * @param string $location
     */
    public function __construct(ContainerInterface $container, string $location)
    {
        $routes = require($location);
        parent::__construct($container, $routes);
    }
}
