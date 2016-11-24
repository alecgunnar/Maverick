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
    const MISSING_ATTRIBUTE_EXCEPTION = 'All routes must have a "%s" attribute. Please check your configuration.';

    /**
     * @var string
     */
    const DEFAULT_METHOD = 'GET';

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function loadRoutes(CollectionInterface $collection)
    {
        $this->parseRoutes($this->routes, $collection);
    }

    protected function parseRoutes(
        array $routes,
        CollectionInterface $collection,
        string $prefix = ''
    ) {
        foreach ($routes as $name => $route) {
            if (isset($route['group'])) {
                $this->parseRoutes(
                    $route['group'],
                    $collection,
                    $this->cleanRoutePath($route['path'])
                );

                continue;
            }

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
    ) {
        $route = $this->processRouteConfig($route);
        $path = $this->cleanRoutePath($route['path'], $prefix);

        $collection->withRoute($route['name'], new Route($route['methods'], $path, $route['call']));
    }

    protected function processRouteConfig(array $route): array
    {
        $this->checkForAttribute($route, 'path');
        $this->checkForAttribute($route, 'call');

        $route['method'] = $route['method'] ?? self::DEFAULT_METHOD;
        $route['methods'] = isset($route['methods']) ? $route['methods'] : [$route['method']];

        if (!is_array($route['methods'])) {
            throw new Exception('The "methods" attribute for all routes must be an array of valid HTTP methods.');
        }

        $route['name'] = $route['name'] ?? $this->generateRouteName($route);

        return $route;
    }

    protected function checkForAttribute(array $values, string $attr)
    {
        if (!isset($values[$attr])) {
            $msg = sprintf(self::MISSING_ATTRIBUTE_EXCEPTION, $attr);
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
