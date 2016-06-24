<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */
declare(strict_types=1);

namespace Maverick\Router\Collection\Decorator;

use Maverick\Router\Collection\RouteCollectionInterface;
use Maverick\Router\Entity\RouteEntityInterface;

abstract class RouteCollectionDecorator implements RouteCollectionInterface
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

    /**
     * @inheritDoc
     */
    public function withRoute(RouteEntityInterface $route, string $name = null): RouteCollectionInterface
    {
        $this->collection->withRoute($route, $name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withRoutes(array $routes): RouteCollectionInterface
    {
        $this->collection->withRoutes($routes);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPrefix(string $prefix): RouteCollectionInterface
    {
        $this->collection->setPrefix($prefix);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRoutes(): array
    {
        return $this->collection->getRoutes();
    }

    /**
     * @inheritDoc
     */
    public function getRoute(string $name)
    {
        return $this->collection->getRoute($name);
    }

    /**
     * @inheritDoc
     */
    public function mergeCollection(RouteCollectionInterface $collection): RouteCollectionInterface
    {
        $this->collection->mergeCollection($collection);
        return $this;
    }
}
