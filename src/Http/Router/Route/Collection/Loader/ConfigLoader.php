<?php

namespace Maverick\Http\Router\Route\Collection\Loader;

use Maverick\Http\Router\Route\Collection\CollectionInterface;
use Maverick\Http\Router\Route\Route;
use Exception;

class ConfigLoader implements LoaderInterface
{
    /**
     * @var array
     */
    protected $routes;

    /**
     * @var string
     */
    const DEFAULT_METHOD = 'GET';

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function loadRoutes(CollectionInterface $collection): void
    {
        $this->parseRoutes($this->routes, $collection);
    }

    protected function parseRoutes(
        array $routes,
        CollectionInterface $collection,
        string $prefix = ''
    ): void {
        foreach ($routes as $name => $route) {
            if (is_string($name)) {
                $route['name'] = $route['name'] ?? $name;
            }

            $this->parseRoute($route, $collection, $prefix);
        }
    }

    protected function parseRoute(
        array $route,
        CollectionInterface $collection,
        string $prefix = ''
    ): void {
        $route = $this->processRouteConfig($route);
        $path = $this->cleanRoutePath($route['path'], $prefix);

        $collection->withRoute($route['name'], new Route($route['methods'], $path, $route['call']));
    }

    protected function processRouteConfig(array $route): array
    {
        $this->checkForAttribute($route, 'path');
        $this->checkForAttribute($route, 'call');

        if (!isset($route['methods'])) {
            $route['method'] = $route['method'] ?? self::DEFAULT_METHOD;
            $route['methods'] = is_array($route['method']) ? $route['method'] : [$route['method']];
        }

        if (!is_array($route['methods'])) {
            throw new Exception('The "methods" attribute for all routes must be an array of valid HTTP methods.');
        }

        $route['name'] = $route['name'] ?? $this->generateRouteName($route);

        return $route;
    }

    protected function checkForAttribute(array $values, string $attr): void
    {
        if (!isset($values[$attr])) {
            $msg = sprintf('All routes must have a "%s" attribute. Please check your configuration.', $attr);
            throw new Exception($msg);
        }
    }

    protected function cleanRoutePath(string $path, string $prefix = ''): string
    {
        return $prefix . '/' . trim($path, '/');
    }

    protected function generateRouteName(array $route): string
    {
        return md5($route['path'] . implode('', $route['methods']));
    }
}
