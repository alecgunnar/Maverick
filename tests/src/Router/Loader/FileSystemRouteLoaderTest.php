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

    protected function getMockContainer()
    {
        return $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorSetsContainer()
    {
        $given = $expected = $this->getMockContainer();

        $location = $this->getMockFilePath();

        $instance = new FileSystemRouteLoader($given, $location);

        $this->assertAttributeSame($expected, 'container', $instance);
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

        $instance = new FileSystemRouteLoader($this->getMockContainer(), $location);

        $this->assertAttributeEquals($data, 'routes', $instance);
    }
}
