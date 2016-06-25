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

class RouteCollectionFactory
{
    /**
     * @return RouteCollectionInterface
     */
    public function build(): RouteCollectionInterface
    {
        return new RouteCollection();
    }
}
