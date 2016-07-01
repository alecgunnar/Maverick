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
use RuntimeException;

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
        string $prefix = null
    ) {
        $this->builder = $builder;
        $this->collectionFactory = $collectionFactory;
        $this->entityFactory = $entityFactory;
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
                foreach ($this->middleware as $middleware) {
                    $route->with($middleware);
                }
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
        throw new RuntimeException('A callback route loader does not accept routes via withRoutes.'
            . ' You must use one of the HTTP request method specific methods, match, or group!');
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function get(string $path, string $name = null): RouteEntityInterface
    {
        return $this->match(['GET'], $path, $name);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function post(string $path, string $name = null): RouteEntityInterface
    {
        return $this->match(['POST'], $path, $name);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function put(string $path, string $name = null): RouteEntityInterface
    {
        return $this->match(['PUT'], $path, $name);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function patch(string $path, string $name = null): RouteEntityInterface
    {
        return $this->match(['PATCH'], $path, $name);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function delete(string $path, string $name = null): RouteEntityInterface
    {
        return $this->match(['DELETE'], $path, $name);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function head(string $path, string $name = null): RouteEntityInterface
    {
        return $this->match(['HEAD'], $path, $name);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function options(string $path, string $name = null): RouteEntityInterface
    {
        return $this->match(['OPTIONS'], $path, $name);
    }

    /**
     * @param string[] $methods
     * @param string $path
     * @param mixed $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function match(array $methods, string $path, string $name = null): RouteEntityInterface
    {
        $entity = $this->entityFactory->build($methods, $path);

        if ($name) {
            return ($this->routes[$name] = $entity);
        }

        return ($this->routes[] = $entity);
    }

    /**
     * @param string $prefix
     * @param callable $builder
     * @return MiddlewareQueueInterface
     */
    public function group(string $prefix, callable $builder): CallbackRouteLoader
    {
        /*
         * @todo Factory-ize this
         */
        $loader = new self(
            $builder,
            $this->collectionFactory,
            $this->entityFactory,
            $prefix
        );

        $this->loaders[] = $loader;

        return $loader;
    }
}
