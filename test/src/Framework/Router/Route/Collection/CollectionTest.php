<?php

namespace Maverick\Http\Router\Route\Collection;

use PHPUnit_Framework_TestCase;
use Maverick\Http\Router\Route\RouteInterface;

class CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testWithRouteAddsRouteToCollection()
    {
        $name = 'named.route';
        $route = $this->getMockRoute();

        $instance = new Collection();

        $instance->withRoute($name, $route);

        $this->assertEquals(1, $instance->count());
        $this->assertEquals([
            $name => $route
        ], $instance->all());
    }

    public function testWithRoutesMergesOtherCollectionIntoSelf()
    {
        $nameA = 'named.routeA';
        $routeA = $this->getMockRoute();

        $nameB = 'named.routeB';
        $routeB = $this->getMockRoute();

        $nameC = 'named.routeC';
        $routeC = $this->getMockRoute();

        $routeD = $this->getMockRoute();

        $instance = new Collection();
        $instance->withRoute($nameA, $routeA);
        $instance->withRoute($nameC, $routeC);

        $collection = new Collection();
        $collection->withRoute($nameB, $routeB);
        $collection->withRoute($nameC, $routeD);

        $instance->withRoutes($collection);

        $this->assertEquals([
            $nameA => $routeA,
            $nameB => $routeB,
            $nameC => $routeD
        ], $instance->all());
    }

    public function testGetRouteReturnsNamedRoute()
    {
        $name = 'test';
        $given = $expected = $this->getMockRoute();

        $instance = new Collection();

        $instance->withRoute($name, $given);

        $this->assertSame($expected, $instance->getRoute($name));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage A route named "test" does not exist.
     */
    public function testGetRouteThrowsExceptionWhenRouteDoesNotExist()
    {
        $instance = new Collection();
        $instance->getRoute('test');
    }

    protected function getMockRoute()
    {
        return $this->getMockBuilder(RouteInterface::class)
            ->getMock();
    }
}
