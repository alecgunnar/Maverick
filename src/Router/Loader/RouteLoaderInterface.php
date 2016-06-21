<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router\Loader;

use Maverick\Router\Collection\RouteCollectionInterface;

interface RouteLoaderInterface
{
    /**
     * @param RouteCollectionInterface $collection
     */
    public function loadRoutes(RouteCollectionInterface $collection);
}
