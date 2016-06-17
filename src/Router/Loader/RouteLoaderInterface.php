<?php
/**
 * Maverick Container
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router\Loader;

use Maverick\Router\Collection\RouteCollectionInterface;

interface RouteLoaderInterface
{
    /**
     * @return bool
     */
    public function loadRoutes(): bool;
}
