<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router\Loader;

use RuntimeException;
use Maverick\Router\Collection\RouteCollectionInterface;
use Maverick\Router\Entity\RouteEntity;

class RouteLoader implements RouteLoaderInterface
{
    /**
     * @var mixed[]
     */
    protected $routes = [];

    /**
     * @param array $routes
     */
    public function __construct(array $routes = [])
    {
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
        $execption = function($field) use($name) {
            throw new RuntimeException(
                'A ' . $field . ' was not provided for route: '
                    . ($name ? $name : 'no name provided')
                    . '.'
            );
        };

        $methods = ['GET'];

        if (isset($data['methods'])) {
            $methods = (array) $data['methods'];
        }

        if (!isset($data['path'])) {
            $execption('path');
        }

        $path = $data['path'];

        if (!isset($data['handler'])) {
            $execption('handler');
        }

        $handler = $data['handler'];

        $entity = new RouteEntity($methods, $path, $handler);

        if (isset($data['middleware']) && is_array($data['middleware'])) {
            $entity->withMiddlewares($data['middleware']);
        }

        return $entity;
    }
}
