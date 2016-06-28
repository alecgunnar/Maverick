<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router\Loader;

class FileSystemRouteLoader extends RouteLoader
{
    /**
     * @param string $location
     */
    public function __construct(string $location)
    {
        $routes = require($location);
        parent::__construct($routes);
    }
}
