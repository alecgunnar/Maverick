<?php

namespace Maverick\Router\Loader;

use PHPUnit_Framework_TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Interop\Container\ContainerInterface;
use Maverick\Router\Collection\RouteCollectionInterface;
use Maverick\Router\Loader\RouteLoaderInterface;

/**
 * @coversDefaultClass Maverick\Router\Loader\FileSystemRouteLoader
 */
class FileSystemRouteLoaderTest extends PHPUnit_Framework_TestCase
{
    protected function getMockFilePath(array $data = [])
    {
        $root = vfsStream::setup();
        $json = json_encode($data);
        $code = <<<CODE
<?php
return json_decode('$json', true);
CODE;

        return vfsStream::newFile(rand() . '.php')
            ->withContent($code)
            ->at($root)
            ->url();
    }

    protected function getMockRouteLoader()
    {
        return $this->getMockBuilder(RouteLoaderInterface::class)
            ->getMock();
    }

    protected function getMockRouteCollection()
    {
        return $this->getMockBuilder(RouteCollectionInterface::class)
            ->getMock();
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorSetsLoader()
    {
        $location = $this->getMockFilePath();
        $given    = $expected = $this->getMockRouteLoader();

        $instance = new FileSystemRouteLoader($location, $given);

        $this->assertAttributeSame($expected, 'loader', $instance);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorLoadsRoutesFromFile()
    {
        $data = [
            'route' => [
                'methods' => ['GET'],
                'path' => '/hello',
                'handler' => 'test.handler'
            ]
        ];

        $location = $this->getMockFilePath($data);
        $loader   = $this->getMockRouteLoader();

        $loader->expects($this->once())
            ->method('withRoutes')
            ->with($data)
            ->willReturn($loader);

        $instance = new FileSystemRouteLoader($location, $loader);
    }

    /**
     * @covers ::loadRoutes
     */
    public function testLoadRoutesCallsLoader()
    {
        $location   = $this->getMockFilePath();
        $collection = $this->getMockRouteCollection();
        $loader     = $this->getMockRouteLoader();

        $loader->expects($this->once())
            ->method('loadRoutes')
            ->with($collection);

        $instance = new FileSystemRouteLoader($location, $loader);

        $instance->loadRoutes($collection);
    }

    /**
     * @covers ::withRoutes
     */
    public function testWithRoutesCallsLoader()
    {
        $data = [
            'route' => [
                'methods' => ['GET'],
                'path' => '/hello',
                'handler' => 'test.handler'
            ]
        ];

        $location   = $this->getMockFilePath();
        $collection = $this->getMockRouteCollection();
        $loader     = $this->getMockRouteLoader();

        $loader->expects($this->at(0))
            ->method('withRoutes')
            ->with([])
            ->willReturn($loader);

        $loader->expects($this->at(1))
            ->method('withRoutes')
            ->with($data)
            ->willReturn($loader);

        $instance = new FileSystemRouteLoader($location, $loader);

        $instance->withRoutes($data);
    }

    /**
     * @covers ::withRoutes
     */
    public function testWithRoutesReturnsSelf()
    {
        $data = [
            'route' => [
                'methods' => ['GET'],
                'path' => '/hello',
                'handler' => 'test.handler'
            ]
        ];

        $location   = $this->getMockFilePath();
        $collection = $this->getMockRouteCollection();
        $loader     = $this->getMockRouteLoader();

        $instance = new FileSystemRouteLoader($location, $loader);

        $ret = $instance->withRoutes($data);

        $this->assertSame($instance, $ret);
    }
}
