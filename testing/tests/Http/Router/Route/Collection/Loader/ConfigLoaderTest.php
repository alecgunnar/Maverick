<?php

namespace Maverick\Http\Router\Route\Collection\Loader;

use PHPUnit_Framework_TestCase;
use Maverick\Http\Router\Route\Route;
use Maverick\Http\Router\Route\Factory\ContainerAwareFactoryInterface;
use Maverick\Http\Router\Route\Collection\CollectionInterface;
use Exception;

class ConfigLoaderTest extends PHPUnit_Framework_TestCase
{
    public function testLoadRoutesBuildsRouteFromConfig()
    {
        $name = 'route.name';
        $method = 'GET';
        $path = '/welcome';
        $service = 'test.service_name';

        $route = new Route([$method], $path, $service);

        $collection = $this->getMockCollection();
        $collection->expects($this->once())
            ->method('withRoute')
            ->with($name, $route);

        $config = [
            $name => [
                'method' => $method,
                'path' => $path,
                'call' => $service
            ]
        ];

        $instance = new ConfigLoader($config);

        $routes = $instance->loadRoutes($collection);
    }

    public function testLoadRoutesBuildsRouteFromConfigWhenMethodIsArray()
    {
        $name = 'route.name';
        $methods = ['GET', 'POST'];
        $path = '/welcome';
        $service = 'test.service_name';

        $route = new Route($methods, $path, $service);

        $collection = $this->getMockCollection();
        $collection->expects($this->once())
            ->method('withRoute')
            ->with($name, $route);

        $config = [
            $name => [
                'method' => $methods,
                'path' => $path,
                'call' => $service
            ]
        ];

        $instance = new ConfigLoader($config);

        $routes = $instance->loadRoutes($collection);
    }

    public function testLoadRoutesNeatensUpThePath()
    {
        $name = 'route.name';
        $method = 'GET';
        $badPath = 'welcome/';
        $goodPath = '/welcome';
        $service = 'test.service_name';

        $route = new Route([$method], $goodPath, $service);

        $collection = $this->getMockCollection();
        $collection->expects($this->once())
            ->method('withRoute')
            ->with($name, $route);

        $config = [
            $name => [
                'method' => $method,
                'path' => $badPath,
                'call' => $service
            ]
        ];

        $instance = new ConfigLoader($config);

        $routes = $instance->loadRoutes($collection);
    }

    public function testLoadRoutesBuildsRouteFromConfigAndDefaultsViaToGet()
    {
        $name = 'route.name';
        $method = 'GET';
        $path = '/welcome';
        $service = 'test.service_name';
        $route = new Route([$method], $path, $service);

        $collection = $this->getMockCollection();
        $collection->expects($this->once())
            ->method('withRoute')
            ->with($name, $route);

        $config = [
            $name => [
                'path' => $path,
                'call' => $service
            ]
        ];

        $instance = new ConfigLoader($config);

        $routes = $instance->loadRoutes($collection);
    }

    public function testLoadRoutesBuildsRouteFromConfigAndDefaultsNameToMd5OfPathAndMethod()
    {
        $method = 'GET';
        $path = '/welcome';
        $name = md5($path . $method);
        $service = 'test.service_name';

        $collection = $this->getMockCollection();
        $collection->expects($this->once())
            ->method('withRoute')
            ->with($name, $this->anything());

        $config = [
            [
                'method' => $method,
                'path' => $path,
                'call' => $service
            ]
        ];

        $instance = new ConfigLoader($config);

        $routes = $instance->loadRoutes($collection);
    }

    public function testLoadRoutesThrowsExceptionIfPathNotProvided()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('All routes must have a "path" attribute. Please check your configuration.');

        $collection = $this->getMockCollection();

        $config = [
            [
                'method' => 'GET',
                'call' => 'service'
            ]
        ];

        $instance = new ConfigLoader($config);

        $routes = $instance->loadRoutes($collection);
    }

    public function testExceptionThrownIfMethodsIsNotAnArray()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('"methods" attribute for all routes must be an array of valid HTTP methods.');

        $collection = $this->getMockCollection();

        $config = [
            [
                'methods' => 'GET',
                'path' => '/test',
                'call' => 'service'
            ]
        ];

        $instance = new ConfigLoader($config);

        $routes = $instance->loadRoutes($collection);
    }

    public function testLoadRoutesThrowsExceptionIfCallNotProvided()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('All routes must have a "call" attribute. Please check your configuration.');

        $collection = $this->getMockCollection();

        $config = [
            [
                'method' => 'GET',
                'path' => '/test'
            ]
        ];

        $instance = new ConfigLoader($config);

        $routes = $instance->loadRoutes($collection);
    }

    public function testLoadRoutesGeneratesNameWhenMultipleMethodsProvided()
    {
        $methodA = 'GET';
        $methodB = 'POST';
        $path = '/test';
        $name = md5($path . $methodA . $methodB);

        $collection = $this->getMockCollection();
        $collection->expects($this->once())
            ->method('withRoute')
            ->with($name, $this->anything());

        $config = [
            [
                'methods' => [$methodA, $methodB],
                'path' => $path,
                'call' => 'service'
            ]
        ];

        $instance = new ConfigLoader($config);

        $instance->loadRoutes($collection);
    }

    protected function getMockCollection()
    {
        return $this->getMockBuilder(CollectionInterface::class)
            ->getMock();
    }
}
