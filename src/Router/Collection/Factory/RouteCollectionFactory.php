<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */
declare(strict_types=1);

namespace Maverick\Router\Collection\Factory;

use Maverick\Router\Collection\RouteCollectionInterface;
use Maverick\Router\Collection\RouteCollection;
use Maverick\Router\Collection\FastRouteRouteCollection;
use Maverick\Router\Collection\ConfigurableRouteCollection;

class RouteCollectionFactory
{
    /**
     * @param string $type = null
     * @return RouteCollectionInterface
     */
    public function build(): RouteCollectionInterface
    {
        return new RouteCollection();
    }
}
