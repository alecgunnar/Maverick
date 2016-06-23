<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router\Loader;

use Interop\Container\ContainerInterface;
use RuntimeException;
use Maverick\Router\Collection\RouteCollectionInterface;
use Maverick\Router\Entity\RouteEntity;

class RouteLoader implements RouteLoaderInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var mixed[]
     */
    protected $routes = [];

    /**
     * @param ContainerInterface $container
     * @param array $routes
     */
    public function __construct(ContainerInterface $container, array $routes = [])
    {
        $this->container = $container;
        $this->routes    = $routes;
    }

    /**
     * @param mixed[] $routes
     * @return self
     */
    public function withRoutes(array $routes): RouteLoaderInterface
    {
        $this->routes = $routes;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function loadRoutes(RouteCollectionInterface $collection)
    {
        foreach ($this->routes as $name => $route) {
            $collection->withRoute(
                $this->processRoute($route, $name),
                $name
            );
        }
    }

    /**
     * @throws RuntimeException
     * @param mixed[] $data
     * @param string $name
     * @return mixed[]
     */
    protected function processRoute(array $data, string $name = null)
    {
        $execptionMsg = function($field) use($name) {
            return 'A ' . $field . ' was not provided for route: '
                    . ($name ? $name : 'no name provided')
                    . '.';
        };

        $methods = ['GET'];

        if (isset($data['methods'])) {
            $methods = (array) $data['methods'];
        }

        if (!isset($data['path'])) {
            throw new RuntimeException($execptionMsg('path'));
        }

        $path = $data['path'];

        if (!isset($data['handler'])) {
            throw new RuntimeException($execptionMsg('handler'));
        }

        $handler = $data['handler'];

        if (!is_callable($handler)) {
            $handler = $this->container->get((string) $handler);
        }

        $entity = new RouteEntity($methods, $path, $handler);

        if (isset($data['middleware'])) {
            foreach ($data['middleware'] as $middleware) {
                if (!is_callable($middleware)) {
                    $middleware = $this->container->get($middleware);
                }

                $entity->withMiddleware($middleware);
            }
        }

        return $entity;
    }
}
