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
use Maverick\Router\Collection\CallbackRouteCollection;

class RouteCollectionFactory
{
    /**
     * @var string
     */
    const FAST_ROUTE = 'FAST_ROUTE';

    /**
     * @var string
     */
    const CALLBACK = 'CALLBACK';

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

            case self::CALLBACK:
                return new CallbackRouteCollection(...$args);
        }

        return new RouteCollection();
    }
}
