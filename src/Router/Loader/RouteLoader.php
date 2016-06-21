<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router\Loader;

use Maverick\Router\Collection\RouteCollectionInterface;
use Maverick\Router\Entity\RouteEntityInterface;
use Maverick\Router\Entity\RouteEntity;

class RouteLoader extends AbstractRouteLoader
{
    /**
     * @inheritDocs
     */
    public function loadRoutes(): RouteCollectionInterface
    {
        return $this->collection;
    }

    /**
     * @param string $path
     * @param callable $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function get(string $path, callable $handler, string $name = null): RouteEntityInterface
    {
        return $this->withRoute([__METHOD__], $path, $handler, $name);
    }

    /**
     * @param string $path
     * @param callable $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function post(string $path, callable $handler, string $name = null): RouteEntityInterface
    {
        return $this->withRoute([__METHOD__], $path, $handler, $name);
    }

    /**
     * @param string $path
     * @param callable $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function delete(string $path, callable $handler, string $name = null): RouteEntityInterface
    {
        return $this->withRoute([__METHOD__], $path, $handler, $name);
    }

    /**
     * @param string $path
     * @param callable $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function head(string $path, callable $handler, string $name = null): RouteEntityInterface
    {
        return $this->withRoute([__METHOD__], $path, $handler, $name);
    }

    /**
     * @param string $path
     * @param callable $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function options(string $path, callable $handler, string $name = null): RouteEntityInterface
    {
        return $this->withRoute([__METHOD__], $path, $handler, $name);
    }

    /**
     * @param string[] $methods
     * @param string $path
     * @param callable $handler
     * @param string $name = null
     * @return RouteEntityInterface
     */
    public function withRoute(array $methods, string $path, callable $handler, string $name = null): RouteEntityInterface
    {
        $entity = new RouteEntity($methods, $path, $handler);

        $this->collection->withRoute($entity, $name);

        return $entity;
    }
}
