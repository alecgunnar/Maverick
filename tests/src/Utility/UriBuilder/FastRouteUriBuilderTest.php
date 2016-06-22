<?php

namespace Maverick\Utility\UriBuilder;

use PHPUnit_Framework_TestCase;
use FastRoute\RouteParser;
use FastRoute\RouteParser\Std as StandardParser;
use Maverick\Router\Collection\RouteCollectionInterface;
use Maverick\Router\Entity\RouteEntityInterface;

/**
 * @coversDefaultClass Maverick\Utility\UriBuilder\FastRouteUriBuilder
 */
class FastRouteUriBuilderTest extends PHPUnit_Framework_TestCase
{
    protected function getMockRouteCollection()
    {
        return $this->getMockBuilder(RouteCollectionInterface::class)
            ->getMock();
    }

    protected function getMockRouteParser()
    {
        return $this->getMockBuilder(RouteParser::class)
            ->getMock();
    }

    protected function getMockRouteEntity()
    {
        return $this->getMockBuilder(RouteEntityInterface::class)
            ->getMock();
    }

    protected function getParser()
    {
        return new StandardParser();
    }

    /**
     * @covers ::__construct
     */
    public function testConstructSetsParser()
    {
        $given = $expected = $this->getMockRouteParser();

        $instance = new FastRouteUriBuilder($given, $this->getMockRouteCollection());

        $this->assertAttributeSame($expected, 'parser', $instance);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructSetsCollection()
    {
        $given = $expected = $this->getMockRouteCollection();

        $instance = new FastRouteUriBuilder($this->getMockRouteParser(), $given);

        $this->assertAttributeSame($expected, 'collection', $instance);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Cannot build URI for route: "test_route" because it does not exist.
     * @covers ::build
     * @covers ::processRequiredParams
     * @covers ::processParam
     */
    public function testBuildThrowsExceptionWhenRouteDoesNotExist()
    {
        $given = $expected = 'test_route';

        $collection = $this->getMockRouteCollection();

        $collection->expects($this->once())
            ->method('getRoute')
            ->with($expected)
            ->willReturn(null);

        $instance = new FastRouteUriBuilder($this->getMockRouteParser(), $collection);

        $instance->build($given);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing param: "name" for route: "test_route".
     * @covers ::build
     * @covers ::processRequiredParams
     * @covers ::processParam
     */
    public function testBuildThrowsExceptionWhenParamNotProvided()
    {
        $name  = 'test_route';
        $param = 'name';
        $path  = '/test/{' . $param . '}';

        $route = $this->getMockRouteEntity();

        $route->expects($this->once())
            ->method('getPath')
            ->willReturn($path);

        $collection = $this->getMockRouteCollection();

        $collection->expects($this->once())
            ->method('getRoute')
            ->with($name)
            ->willReturn($route);

        $instance = new FastRouteUriBuilder($this->getParser(), $collection);

        $instance->build($name);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Param value: "123" does not match expected format: "A-Z" for route: "test_route".
     * @covers ::build
     * @covers ::processRequiredParams
     * @covers ::processParam
     */
    public function testBuildThrowsExceptionWhenParamDoesNotMatch()
    {
        $name  = 'test_route';
        $param = 'name';
        $regex = 'A-Z';
        $path  = '/test/{' . $param . ':' . $regex . '}';

        $route = $this->getMockRouteEntity();

        $route->expects($this->once())
            ->method('getPath')
            ->willReturn($path);

        $collection = $this->getMockRouteCollection();

        $collection->expects($this->once())
            ->method('getRoute')
            ->with($name)
            ->willReturn($route);

        $instance = new FastRouteUriBuilder($this->getParser(), $collection);

        $instance->build($name, [
            $param => '123'
        ]);
    }

    /**
     * @covers ::build
     * @covers ::processRequiredParams
     * @covers ::processParam
     */
    public function testBuildCreatesUriWithoutParams()
    {
        $name  = 'boring_route';
        $given = $expected = '/plain/and/simple';

        $route = $this->getMockRouteEntity();

        $route->expects($this->once())
            ->method('getPath')
            ->willReturn($given);

        $collection = $this->getMockRouteCollection();

        $collection->expects($this->once())
            ->method('getRoute')
            ->with($name)
            ->willReturn($route);

        $instance = new FastRouteUriBuilder($this->getParser(), $collection);

        $this->assertEquals($expected, (string) $instance->build($name));
    }

    /**
     * @covers ::build
     * @covers ::processRequiredParams
     * @covers ::processParam
     */
    public function testBuildCreatesUriWithParams()
    {
        $name     = 'parameterized_route';
        $param    = 'name';
        $given    = '/hello/{' . $param . '}';
        $value    = 'world';
        $expected = '/hello/' . $value;

        $route = $this->getMockRouteEntity();

        $route->expects($this->once())
            ->method('getPath')
            ->willReturn($given);

        $collection = $this->getMockRouteCollection();

        $collection->expects($this->once())
            ->method('getRoute')
            ->with($name)
            ->willReturn($route);

        $instance = new FastRouteUriBuilder($this->getParser(), $collection);

        $this->assertEquals($expected, (string) $instance->build($name, [
            $param => $value
        ]));
    }

    /**
     * @covers ::build
     * @covers ::processRequiredParams
     * @covers ::processOptionalParam
     * @covers ::processParam
     */
    public function testBuildCreatesUriWithOptionalParamProvided()
    {
        $name     = 'parameterized_route';
        $param    = 'name';
        $given    = '/hello[/{' . $param . '}]';
        $value    = 'world';
        $expected = '/hello/' . $value;

        $route = $this->getMockRouteEntity();

        $route->expects($this->once())
            ->method('getPath')
            ->willReturn($given);

        $collection = $this->getMockRouteCollection();

        $collection->expects($this->once())
            ->method('getRoute')
            ->with($name)
            ->willReturn($route);

        $instance = new FastRouteUriBuilder($this->getParser(), $collection);

        $this->assertEquals($expected, (string) $instance->build($name, [
            $param => $value
        ]));
    }

    /**
     * @covers ::build
     * @covers ::processRequiredParams
     * @covers ::processOptionalParam
     * @covers ::processParam
     */
    public function testBuildCreatesUriWithOptionalParamNotProvided()
    {
        $name     = 'parameterized_route';
        $param    = 'name';
        $given    = '/hello[/{' . $param . '}]';
        $expected = '/hello';

        $route = $this->getMockRouteEntity();

        $route->expects($this->once())
            ->method('getPath')
            ->willReturn($given);

        $collection = $this->getMockRouteCollection();

        $collection->expects($this->once())
            ->method('getRoute')
            ->with($name)
            ->willReturn($route);

        $instance = new FastRouteUriBuilder($this->getParser(), $collection);

        $this->assertEquals($expected, (string) $instance->build($name));
    }
}
