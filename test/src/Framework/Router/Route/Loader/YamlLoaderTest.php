<?php

namespace Maverick\Http\Router\Route\Loader;

use PHPUnit_Framework_TestCase;
use Maverick\Http\Router\Route\Route;
use Maverick\Http\Router\Route\Factory\ContainerAwareFactoryInterface;
use Maverick\Http\Router\Route\Collection\CollectionInterface;
use org\bovigo\vfs\vfsStream;

class YamlLoaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The route configuration file "does-not-exist.yml" does not exist.
     */
    public function testExceptionThrownWhenFileDoesNotExist()
    {
        $collection = $this->getMockCollection();

        $instance = $this->getInstance();
        $instance->loadRoutes('does-not-exist.yml', $collection);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessageRegExp /The route configuration file ".+" does not contain valid YAML. The error was: .+/
     */
    public function testExceptionThrownWhenFileIsNotValidYaml()
    {
        $collection = $this->getMockCollection();

        $root = vfsStream::setup();
        $file = vfsStream::newFile('invalid-yaml.yml')
            ->withContent('{ a <=> b ')
            ->at($root);

        $instance = $this->getInstance();
        $instance->loadRoutes($file->url(), $collection);
    }

    public function testLoadRoutesBuildsRouteFromConfig()
    {
        $name = 'route.name';
        $methods = 'GET';
        $path = '/welcome';
        $service = 'test';
        $callable = function () { };
        $middleware = [function () { }];

        $config = <<<YAML
- name: $name
  via: $methods
  path: $path
  call: $service
  stack:
    - $service
YAML;

        $root = vfsStream::setup();
        $file = vfsStream::newFile('routes.yml')
            ->withContent($config)
            ->at($root);

        $route = new Route([$methods], $path, $callable, $middleware);

        $collection = $this->getMockCollection();
        $collection->expects($this->once())
            ->method('withRoute')
            ->with($name, $route);

        $factory = $this->getMockFactory();
        $factory->expects($this->once())
            ->method('buildRoute')
            ->with($methods, $path, $service, [$service])
            ->willReturn($route);

        $instance = $this->getInstance($factory);

        $routes = $instance->loadRoutes($file->url(), $collection);
    }

    public function testLoadRoutesBuildsRouteFromConfigAndIgnoresDuplicateMiddleware()
    {
        $name = 'route.name';
        $methods = 'GET';
        $path = '/welcome';
        $service = 'test';
        $callable = function () { };
        $middleware = [function () { }];

        $config = <<<YAML
- name: $name
  via: $methods
  path: $path
  call: $service
  stack:
    - $service
    - $service
YAML;

        $root = vfsStream::setup();
        $file = vfsStream::newFile('routes.yml')
            ->withContent($config)
            ->at($root);

        $route = new Route([$methods], $path, $callable, $middleware);

        $collection = $this->getMockCollection();
        $collection->expects($this->once())
            ->method('withRoute')
            ->with($name, $route);

        $factory = $this->getMockFactory();
        $factory->expects($this->once())
            ->method('buildRoute')
            ->with($methods, $path, $service, [$service])
            ->willReturn($route);

        $instance = $this->getInstance($factory);

        $routes = $instance->loadRoutes($file->url(), $collection);
    }

    public function testLoadRoutesBuildsGroupedRoutesFromConfig()
    {
        $name = 'route.name';
        $methods = 'GET';
        $prefix = '/prefix';
        $path = '/welcome';
        $serviceA = 'security';
        $serviceB = 'test';
        $callableA = function () { };
        $callableB = function () { };
        $middleware = [$callableA, $callableB];

        $config = <<<YAML
- path: $prefix
  stack:
    - $serviceA
  group:
    - name: $name
      path: $path
      via: $methods
      call: $serviceB
      stack:
        - $serviceB
YAML;

        $root = vfsStream::setup();
        $file = vfsStream::newFile('routes.yml')
            ->withContent($config)
            ->at($root);

        $route = new Route([$methods], $prefix . $path, $callableA, $middleware);

        $collection = $this->getMockCollection();
        $collection->expects($this->once())
            ->method('withRoute')
            ->with($name, $route);

        $factory = $this->getMockFactory();
        $factory->expects($this->once())
            ->method('buildRoute')
            ->with($methods, $prefix . $path, $serviceB, [$serviceA, $serviceB])
            ->willReturn($route);

        $instance = $this->getInstance($factory);

        $routes = $instance->loadRoutes($file->url(), $collection);
    }

    public function testLoadRoutesBuildsGroupedRoutesFromConfigAndIgnoresDuplicateMiddleware()
    {
        $name = 'route.name';
        $methods = 'GET';
        $prefix = '/prefix';
        $path = '/welcome';
        $serviceA = 'security';
        $serviceB = 'test';
        $callableA = function () { };
        $callableB = function () { };
        $middleware = [$callableA, $callableB];

        $config = <<<YAML
- path: $prefix
  stack:
    - $serviceA
    - $serviceB
  group:
    - name: $name
      path: $path
      via: $methods
      call: $serviceB
      stack:
        - $serviceB
YAML;

        $root = vfsStream::setup();
        $file = vfsStream::newFile('routes.yml')
            ->withContent($config)
            ->at($root);

        $route = new Route([$methods], $prefix . $path, $callableA, $middleware);

        $collection = $this->getMockCollection();
        $collection->expects($this->once())
            ->method('withRoute')
            ->with($name, $route);

        $factory = $this->getMockFactory();
        $factory->expects($this->once())
            ->method('buildRoute')
            ->with($methods, $prefix . $path, $serviceB, [$serviceA, $serviceB])
            ->willReturn($route);

        $instance = $this->getInstance($factory);

        $routes = $instance->loadRoutes($file->url(), $collection);
    }

    public function testLoadRoutesNeatensUpThePath()
    {
        $name = 'route.name';
        $methods = 'GET';
        $badPath = 'welcome/';
        $goodPath = '/welcome';
        $service = 'test';
        $callable = function () { };
        $middleware = [function () { }];

        $config = <<<YAML
- name: $name
  via: $methods
  path: $badPath
  call: $service
  stack:
    - $service
YAML;

        $root = vfsStream::setup();
        $file = vfsStream::newFile('routes.yml')
            ->withContent($config)
            ->at($root);

        $route = new Route([$methods], $goodPath, $callable, $middleware);

        $collection = $this->getMockCollection();
        $collection->expects($this->once())
            ->method('withRoute')
            ->with($name, $route);

        $factory = $this->getMockFactory();
        $factory->expects($this->once())
            ->method('buildRoute')
            ->with($methods, $goodPath, $service, [$service])
            ->willReturn($route);

        $instance = $this->getInstance($factory);

        $routes = $instance->loadRoutes($file->url(), $collection);
    }

    public function testLoadRoutesBuildsGroupedRoutesAndNeatensUpPath()
    {
        $name = 'route.name';
        $methods = 'GET';
        $badPrefix = 'prefix/';
        $goodPrefix = '/prefix';
        $badPath = 'welcome/';
        $goodPath = '/welcome';
        $serviceA = 'security';
        $serviceB = 'test';
        $callableA = function () { };
        $callableB = function () { };
        $middleware = [$callableA, $callableB];

        $config = <<<YAML
- path: $badPrefix
  stack:
    - $serviceA
    - $serviceB
  group:
    - name: $name
      path: $badPath
      via: $methods
      call: $serviceB
      stack:
        - $serviceB
YAML;

        $root = vfsStream::setup();
        $file = vfsStream::newFile('routes.yml')
            ->withContent($config)
            ->at($root);

        $route = new Route([$methods], $goodPrefix . $goodPath, $callableA, $middleware);

        $collection = $this->getMockCollection();
        $collection->expects($this->once())
            ->method('withRoute')
            ->with($name, $route);

        $factory = $this->getMockFactory();
        $factory->expects($this->once())
            ->method('buildRoute')
            ->with($methods, $goodPrefix . $goodPath, $serviceB, [$serviceA, $serviceB])
            ->willReturn($route);

        $instance = $this->getInstance($factory);

        $routes = $instance->loadRoutes($file->url(), $collection);
    }

    public function testLoadRoutesBuildsRouteFromConfigAndDefaultsViaToGet()
    {
        $name = 'route.name';
        $methods = 'GET';
        $path = '/welcome';
        $service = 'test';
        $callable = function () { };

        $config = <<<YAML
- name: $name
  path: $path
  call: $service
YAML;

        $root = vfsStream::setup();
        $file = vfsStream::newFile('routes.yml')
            ->withContent($config)
            ->at($root);

        $route = new Route([$methods], $path, $callable);

        $collection = $this->getMockCollection();
        $collection->expects($this->once())
            ->method('withRoute')
            ->with($name, $route);

        $factory = $this->getMockFactory();
        $factory->expects($this->once())
            ->method('buildRoute')
            ->with($methods, $path, $service)
            ->willReturn($route);

        $instance = $this->getInstance($factory);

        $routes = $instance->loadRoutes($file->url(), $collection);
    }

    public function testLoadRoutesBuildsRouteFromConfigAndDefaultsNameToMd5OfPathAndVia()
    {
        $methods = 'GET';
        $path = '/welcome';
        $name = md5($path . $methods);
        $service = 'test';
        $callable = function () { };

        $config = <<<YAML
- path: $path
  via: $methods
  call: $service
YAML;

        $root = vfsStream::setup();
        $file = vfsStream::newFile('routes.yml')
            ->withContent($config)
            ->at($root);

        $route = new Route([$methods], $path, $callable);

        $collection = $this->getMockCollection();
        $collection->expects($this->once())
            ->method('withRoute')
            ->with($name, $route);

        $factory = $this->getMockFactory();
        $factory->expects($this->once())
            ->method('buildRoute')
            ->with($methods, $path, $service)
            ->willReturn($route);

        $instance = $this->getInstance($factory);

        $routes = $instance->loadRoutes($file->url(), $collection);
    }

    public function testLoadRoutesBuildsRouteFromConfigAndDefaultsStackToEmptyArray()
    {
        $name = 'route.name';
        $methods = 'GET';
        $path = '/welcome';
        $service = 'test';
        $callable = function () { };

        $config = <<<YAML
- name: $name
  path: $path
  via: $methods
  call: $service
YAML;

        $root = vfsStream::setup();
        $file = vfsStream::newFile('routes.yml')
            ->withContent($config)
            ->at($root);

        $route = new Route([$methods], $path, $callable);

        $collection = $this->getMockCollection();
        $collection->expects($this->once())
            ->method('withRoute')
            ->with($name, $route);

        $factory = $this->getMockFactory();
        $factory->expects($this->once())
            ->method('buildRoute')
            ->with($methods, $path, $service, [])
            ->willReturn($route);

        $instance = $this->getInstance($factory);

        $routes = $instance->loadRoutes($file->url(), $collection);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage All routes must have a "path" attribute. Please check your configuration.
     */
    public function testLoadRoutesThrowsExceptionIfPathNotProvided()
    {
        $config = <<<YAML
- call: service
  via: GET
YAML;

        $root = vfsStream::setup();
        $file = vfsStream::newFile('routes.yml')
            ->withContent($config)
            ->at($root);

        $collection = $this->getMockCollection();

        $instance = $this->getInstance();

        $routes = $instance->loadRoutes($file->url(), $collection);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage All routes must have a "call" attribute. Please check your configuration.
     */
    public function testLoadRoutesThrowsExceptionIfCallNotProvided()
    {
        $config = <<<YAML
- path: /
  via: GET
YAML;

        $root = vfsStream::setup();
        $file = vfsStream::newFile('routes.yml')
            ->withContent($config)
            ->at($root);

        $collection = $this->getMockCollection();

        $instance = $this->getInstance();

        $routes = $instance->loadRoutes($file->url(), $collection);
    }

    public function testLoadRoutesGeneratesNameWhenMultipleMethodsProvided()
    {
        $methodA = 'GET';
        $methodB = 'POST';
        $path = '/hello';
        $name = md5($path . $methodA . $methodB);

        $config = <<<YAML
- path: $path
  via: [$methodA, $methodB]
  call: handler
YAML;

        $root = vfsStream::setup();
        $file = vfsStream::newFile('routes.yml')
            ->withContent($config)
            ->at($root);

        $collection = $this->getMockCollection();
        $collection->expects($this->once())
            ->method('withRoute')
            ->with($name, $this->anything());

        $instance = $this->getInstance();

        $routes = $instance->loadRoutes($file->url(), $collection);
    }

    public function getMockFactory()
    {
        return $this->getMockBuilder(ContainerAwareFactoryInterface::class)
            ->getMock();
    }

    public function getMockCollection()
    {
        return $this->getMockBuilder(CollectionInterface::class)
            ->getMock();
    }

    public function getInstance(ContainerAwareFactoryInterface $factory = null)
    {
        return new YamlLoader($factory ?? $this->getMockFactory());
    }
}
