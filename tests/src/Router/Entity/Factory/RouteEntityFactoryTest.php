<?php

namespace Maverick\Router\Entity\Factory;

use PHPUnit_Framework_TestCase;
use Maverick\Router\Entity\RouteEntity;

/**
 * @coversDefaultClass Maverick\Router\Entity\Factory\RouteEntityFactory
 */
class RouteEntityFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::build
     */
    public function testBuildCreatesFastRouteEntity()
    {
        $methods = ['GET', 'POST'];
        $path = '/hello';
        $handler = function() { };

        $expected = new RouteEntity($methods, $path, $handler);

        $instance = new RouteEntityFactory();

        $this->assertEquals($expected, $instance->build($methods, $path, $handler));
    }
}
