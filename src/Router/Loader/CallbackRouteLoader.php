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
use Maverick\Router\Collection\RouteCollectionInterface;
use Maverick\Router\Entity\Factory\RouteEntityFactory;
use Maverick\Router\Entity\RouteEntityInterface;
use Interop\Container\ContainerInterface;

class CallbackRouteLoader implements RouteLoaderInterface, MiddlewareQueueInterface
{
    use MiddlewareQueueTrait;

    /**
     * @var callable
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
     * @param callable $builder
     * @param ContainerInterface $container
     */
    public function __construct(
        callable $builder,
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
        call_user_func_array($this->builder, [$this]);

        if (count($this->middleware)) {
            foreach ($this->routes as $route) {
                $route->withMiddlewares($this->middleware);
            }
        }

        if ($this->prefix) {
            $collection->setPrefix($this->prefix);
        }

        $collection->withRoutes($this->routes);

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
    public function get(string $path, $handler, string $name = null): RouteEntityInterface
    {
        return $this->match(['GET'], $path, $handler, $name);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function post(string $path, $handler, string $name = null): RouteEntityInterface
    {
        return $this->match(['POST'], $path, $handler, $name);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function put(string $path, $handler, string $name = null): RouteEntityInterface
    {
        return $this->match(['PUT'], $path, $handler, $name);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function patch(string $path, $handler, string $name = null): RouteEntityInterface
    {
        return $this->match(['PATCH'], $path, $handler, $name);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function delete(string $path, $handler, string $name = null): RouteEntityInterface
    {
        return $this->match(['DELETE'], $path, $handler, $name);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function head(string $path, $handler, string $name = null): RouteEntityInterface
    {
        return $this->match(['HEAD'], $path, $handler, $name);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function options(string $path, $handler, string $name = null): RouteEntityInterface
    {
        return $this->match(['OPTIONS'], $path, $handler, $name);
    }

    /**
     * @param string[] $methods
     * @param string $path
     * @param mixed $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function match(array $methods, string $path, $handler, string $name = null): RouteEntityInterface
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
    public function group(string $prefix, callable $builder): MiddlewareQueueInterface
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
