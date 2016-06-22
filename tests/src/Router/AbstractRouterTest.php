<?php

namespace Maverick\Router;

use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Maverick\Router\Entity\RouteEntityInterface;

/**
 * @coversDefaultClass Maverick\Router\AbstractRouter
 */
class AbstractRouterTest extends PHPUnit_Framework_TestCase
{
    protected function getMockRouteEntity()
    {
        return $this->getMockForAbstractClass(RouteEntityInterface::class);
    }

    /**
     * @covers ::getMatchedRoute
     */
    public function testGetMatchedRouteReturnsParams()
    {
        $given = $expected = $this->getMockRouteEntity();

        $instance = new class($given) extends AbstractRouter {
            public function __construct(RouteEntityInterface $entity)
            {
                $this->matched = $entity;
            }

            public function checkRequest(ServerRequestInterface $request): int
            {

            }
        };

        $this->assertEquals($expected, $instance->getMatchedRoute());
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

            public function checkRequest(ServerRequestInterface $request): int
            {

            }
        };

        $this->assertEquals($expected, $instance->getParams());
    }

    /**
     * @covers ::getAllowedMethods
     */
    public function testgetAllowedMethodsReturnsParams()
    {
        $given = $expected = ['GET', 'POST', 'PUT'];

        $instance = new class($given) extends AbstractRouter {
            public function __construct(array $methods)
            {
                $this->methods = $methods;
            }

            public function checkRequest(ServerRequestInterface $request): int
            {

            }
        };

        $this->assertEquals($expected, $instance->getAllowedMethods());
    }
}
