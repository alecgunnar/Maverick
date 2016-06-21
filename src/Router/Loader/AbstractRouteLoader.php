<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router\Loader;

use Maverick\Router\Collection\RouteCollectionInterface;

abstract class AbstractRouteLoader
{
    /**
     * @var RouteCollectionInterface
     */
    protected $collection;

    /**
     * @param RouteCollectionInterface $collection
     */
    public function __construct(RouteCollectionInterface $collection)
    {
        $this->collection = $collection;
    }

    abstract public function loadRoutes(): RouteCollectionInterface;
}
