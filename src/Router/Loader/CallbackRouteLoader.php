<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router\Loader;

use Maverick\Middleware\Queue\MiddlewareQueueInterface;
use Maverick\Middleware\Queue\MiddlewareQueueTrait;
use Maverick\Router\Collection\Factory\RouteCollectionFactory;
use Maverick\Router\Entity\Factory\RouteEntityFactory;
use Interop\Container\ContainerInterface;

class CallbackRouteLoader implements RouteLoaderInterface, MiddlewareQueueInterface
{
    use MiddlewareQueueTrait;

    /**
     * @var callback
     */
    protected $builder;

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
     * @var string
     */
    protected $prefix;

    /**
     * @var RouteEntityInterface[]
     */
    protected $routes = [];

    /**
     * @var CallbackRouteLoader[]
     */
    protected $loaders = [];

    /**
     * @param callback $builder
     * @param ContainerInterface $container
     */
    public function __construct(
        callback $builder,
        RouteCollectionFactory $collectionFactory,
        RouteEntityFactory $entityFactory,
        ContainerInterface $container,
        string $prefix = null
    ) {
        $this->builder = $builder;
        $this->collectionFactory = $collectionFactory;
        $this->entityFactory = $entityFactory;
        $this->container = $container;
        $this->prefix = $prefix;
    }

    /**
     * @inheritDocs
     */
    public function loadRoutes(RouteCollectionInterface $collection)
    {
        $collection->setPrefix($this->prefix)
            ->withRoutes($this->routes);

        foreach ($this->loaders as $loader) {
            $loaderCollection = $this->collectionFactory->build();
            $loader->loadRoutes($loaderCollection);
            $collection->mergeCollection($loaderCollection);
        }
    }

    /**
     * @inheritDocs
     */
    public function withRoutes(array $routes): RouteLoaderInterface
    {
        throw new RuntimeException('A callback route loader does not accept routes via withRoutes...');
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function get(string $path, $handler, string $name = null): CallbackRouteLoader
    {
        return $this->match([__METHOD__], $path, $handler, $name);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function post(string $path, $handler, string $name = null): CallbackRouteLoader
    {
        return $this->match([__METHOD__], $path, $handler, $name);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function put(string $path, $handler, string $name = null): CallbackRouteLoader
    {
        return $this->match([__METHOD__], $path, $handler, $name);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function patch(string $path, $handler, string $name = null): CallbackRouteLoader
    {
        return $this->match([__METHOD__], $path, $handler, $name);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function delete(string $path, $handler, string $name = null): CallbackRouteLoader
    {
        return $this->match([__METHOD__], $path, $handler, $name);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function head(string $path, $handler, string $name = null): CallbackRouteLoader
    {
        return $this->match([__METHOD__], $path, $handler, $name);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function options(string $path, $handler, string $name = null): CallbackRouteLoader
    {
        return $this->match([__METHOD__], $path, $handler, $name);
    }

    /**
     * @param string[] $methods
     * @param string $path
     * @param mixed $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function match(array $methods, string $path, $handler, string $name = null): CallbackRouteLoader
    {
        if (!is_callable($handler)) {
            $handler = $this->container->get($handler);
        }

        $entity = $this->entityFactory->build($methods, $path, $handler);

        if ($name) {
            $this->routes[$name] = $entity;
        } else {
            $this->routes[] = $entity;
        }

        return $entity;
    }

    /**
     * @param string $prefix
     * @param callable $builder
     * @return MiddlewareQueueInterface
     */
    public function group(string $prefix, callable $builder)
    {
        $loader = new self(
            $builder,
            $this->collectionFactory,
            $this->entityFactory,
            $this->container,
            $prefix
        );

        $this->loaders[] = $loader;

        return $loader;
    }
}
