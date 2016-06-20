<?php

namespace Maverick\Router;

use PHPUnit_Framework_TestCase;
use Maverick\Router\Collection\RouteCollectionInterface;

/**
 * @coversDefaultClass Maverick\Router\FastRouteRouter
 */
class ApplicationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructSetsCollection()
    {
        $given = $expected = $this->getMockBuilder(RouteCollectionInterface::class)
            ->getMock();

        $instance = new FastRouteRouter($given);

        $this->assertAttributeSame($expected, 'collection', $instance);
    }

    /**
     * @covers ::handleRequest
     */
    public function testHandleRequestReturnsCorrectRoute()
    {
        
    }
}
