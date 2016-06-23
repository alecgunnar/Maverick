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
     * @var string
     */
    const FAST_ROUTE = 'FAST_ROUTE';

    /**
     * @param string $type = null
     * @return RouteCollectionInterface
     */
    public function build(string $type = null): RouteCollectionInterface
    {
        switch ($type) {
            case self::FAST_ROUTE:
                return new FastRouteRouteCollection();
        }

        return new RouteCollection();
    }
}
