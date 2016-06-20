<?php
/**
 * Maverick Container
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router\Collection;

use Maverick\Router\Entity\RouteEntityInterface;

class RouteCollection implements RouteCollectionInterface
{
    /**
     * @inheritDoc
     */
    public function withRoute(RouteEntityInterface $route, string $name = null): RouteCollectionInterface
    {

    }

    /**
     * @inheritDoc
     */
    public function getRoute(string $name): RouteEntityInterface
    {

    }

    /**
     * @inheritDoc
     */
    public function mergeCollection(RouteCollectionInterface $collection): RouteCollectionInterface
    {

    }

    public function current()
    {

    }

    public function key()
    {

    }

    public function next()
    {

    }

    public function rewind()
    {

    }

    public function valid()
    {

    }
}
