<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */
declare(strict_types=1);

namespace Maverick\Router\Collection;

use Maverick\Router\Collection\Factory\RouteCollectionFactory;
use Maverick\Router\Collection\RouteCollectionInterface;
use Maverick\Router\Entity\Factory\RouteEntityFactory;
use Maverick\Router\Entity\RouteEntityInterface;
use Maverick\Middleware\Queue\MiddlewareQueueInterface;
use Maverick\Middleware\Queue\MiddlewareQueueTrait;
use Interop\Container\ContainerInterface;

class CallbackRouteCollection extends RouteCollection implements MiddlewareQueueInterface
{
    use MiddlewareQueueTrait;

    /**
     * @var RouteCollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var RouteEntityFactory
     */
    protected $entityFactory;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var bool
     */
    protected $built;

    /**
     * @param RouteCollectionFactory $collection
     * @param RouteEntityFactory $entity
     * @param ContainerInterface $container
     */
    public function __construct(
        RouteCollectionFactory $collection,
        RouteEntityFactory $entity,
        ContainerInterface $container
    ) {
        $this->collectionFactory = $collection;
        $this->entityFactory = $entity;
        $this->container = $container;
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @return RouteEntityInterface
     */
    public function get(string $path, $handler): RouteEntityInterface
    {
        return $this->with([__METHOD__], $path, $handler);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @return RouteEntityInterface
     */
    public function post(string $path, $handler): RouteEntityInterface
    {
        return $this->with([__METHOD__], $path, $handler);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @return RouteEntityInterface
     */
    public function put(string $path, $handler): RouteEntityInterface
    {
        return $this->with([__METHOD__], $path, $handler);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @return RouteEntityInterface
     */
    public function patch(string $path, $handler): RouteEntityInterface
    {
        return $this->with([__METHOD__], $path, $handler);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @return RouteEntityInterface
     */
    public function delete(string $path, $handler): RouteEntityInterface
    {
        return $this->with([__METHOD__], $path, $handler);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @return RouteEntityInterface
     */
    public function head(string $path, $handler): RouteEntityInterface
    {
        return $this->with([__METHOD__], $path, $handler);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @return RouteEntityInterface
     */
    public function options(string $path, $handler): RouteEntityInterface
    {
        return $this->with([__METHOD__], $path, $handler);
    }

    /**
     * @param string $prefix
     * @param callable $builder
     * @return ConfigureableRouteCollection
     */
    public function group(string $prefix, callable $builder): ConfigureableRouteCollection
    {
        $collection = $this->collectionFactory->build(
            RouteCollectionFactory::CALLBACK,
            $this->collectionFactory,
            $this->entityFactory,
            $this->container
        );

        $builder($collection);

        $this->mergeCollection($collection);

        return $collection;
    }

    /**
     * @param array $methods
     * @param string $path
     * @param mixed $handler
     * @return RouteEntityInterface
     */
    public function with(array $methods, string $path, $handler): RouteEntityInterface
    {
        $entity = $this->entityFactory->build($methods, $path, $handler);

        $this->withRoute($entity);

        return $handler;
    }

    /**
     * @return RouteEntityInterface[]
     */
    public function getRoutes()
    {
        if ($this->built !== true) {
            foreach ($this->routes as $route) {
                $route->withMiddleswares($this->middleware);
            }

            $this->built = true;
        }

        return $this->routes;
    }
}
