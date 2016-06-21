<?php

namespace Maverick\Router;

use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @coversDefaultClass Maverick\Router\AbstractRouter
 */
class AbstractRouterTest extends PHPUnit_Framework_TestCase
{
    protected function getMockAbstractRouter()
    {
        return $this->getMockForAbstractClass(AbstractRouter::class);
    }

    /**
     * @covers ::setNotFoundHandler
     */
    public function testSetNotFoundHandlerSetsHandler()
    {
        $given = $expected = function() { };

        $instance = $this->getMockAbstractRouter();

        $instance->setNotFoundHandler($given);

        $this->assertAttributeEquals($expected, 'notFoundHandler', $instance);
    }

    /**
     * @covers ::setNotFoundHandler
     */
    public function testSetNotFoundHandlerReturnsSelf()
    {
        $instance = $this->getMockAbstractRouter();

        $this->assertSame($instance, $instance->setNotFoundHandler(function() { }));
    }

    /**
     * @covers ::setNotAllowedHandler
     */
    public function testSetNotAllowedHandlerSetsHandler()
    {
        $given = $expected = function() { };

        $instance = $this->getMockAbstractRouter();

        $instance->setNotAllowedHandler($given);

        $this->assertAttributeEquals($expected, 'notAllowedHandler', $instance);
    }

    /**
     * @covers ::setNotAllowedHandler
     */
    public function testSetNotAllowedHandlerReturnsSelf()
    {
        $instance = $this->getMockAbstractRouter();

        $this->assertSame($instance, $instance->setNotAllowedHandler(function() { }));
    }

    /**
     * @covers ::getParams
     */
    public function testGetParamsReturnsParams()
    {
        $given = $expected = [
            'hello' => 'Earth',
            'from'  => 'Mars'
        ];

        $instance = new class($given) extends AbstractRouter {
            public function __construct(array $params)
            {
                $this->params = $params;
            }

            public function handleRequest(ServerRequestInterface $request): callable
            {

            }
        };

        $this->assertEquals($expected, $instance->getParams());
    }
}
