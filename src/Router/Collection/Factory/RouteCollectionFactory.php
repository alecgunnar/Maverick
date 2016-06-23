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
    const FAST_ROUTE = 'FastRoute';

    /**
     * @var string
     */
    const CONFIGURABLE = 'Configurable';

    /**
     * @param string $type = null
     * @return RouteCollectionInterface
     */
    public function build(string $type = null): RouteCollectionInterface
    {
        if (count($args = func_get_args())) {
            $args = array_slice($args, 1);
        }

        switch ($type) {
            case self::FAST_ROUTE:
                return new FastRouteRouteCollection();

            case self::CONFIGURABLE:
                return new ConfigurableRouteCollection(...$args);
        }

        return new RouteCollection();
    }
}
