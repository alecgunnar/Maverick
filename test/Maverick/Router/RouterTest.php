<?php
/**
 * Maverick
 *
 * @package Maverick
 * @author  Alec Carpenter <gunnar94@me.com>
 */

use Maverick\Router\Router;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers Maverick\Router\Router
 */
class RouterTest extends PHPUnit_Framework_TestCase
{
    const CLASS_NAME  = '\\Maverick\\Router\\Router';

    protected function getInstance($collection=null)
    {
        return new Router($collection ?: new RouteCollection());
    }

    public function testForClassAttributes()
    {
        $this->getInstance();
        $this->assertClassHasAttribute('collection', self::CLASS_NAME);
    }

    /**
     * @covers Maverick\Router\Router::__construct
     */
    public function testConstructorSetsCollection()
    {
        $collection = new RouteCollection();
        $instance   = new Router($collection);

        $this->assertAttributeEquals($collection, 'collection', $instance);
    }

    /**
     * @covers Maverick\Router\Router::getCollection
     */
    public function testGetCollection()
    {
        $collection = new RouteCollection();
        $instance   = $this->getInstance($collection);

        $this->assertEquals($instance->getCollection(), $collection);
    }

    /**
     * @covers Maverick\Router\Router::matchRequest
     */
    public function testMatchRequestWithDefinedRoute()
    {
        $routeName      = 'test-route';
        $controllerName = 'test.controller';
        $routePath      = '/test-route';
        $expected       = [
            '_route'      => $routeName,
            '_controller' => $controllerName
        ];

        $instance = $this->getInstance();

        $instance->getCollection()->add($routeName, new Route(
            $routePath, [
                '_controller' => $controllerName
            ]
        ));

        $this->assertEquals($instance->matchRequest(Request::create($routePath)), $expected);
    }

    /**
     * @covers Maverick\Router\Router::matchRequest
     */
    public function testMatchRequestWithUndefinedRoute()
    {
        $routePath = '/undefined-route';
        $expected  = false;

        $this->assertEquals($this->getInstance()->matchRequest(Request::create($routePath)), $expected);
    }

    /**
     * @covers Maverick\Router\Router::matchRequest
     */
    public function testMatchRequestWithDefinedRouteAndParams()
    {
        $routeName        = 'test-route';
        $controllerName   = 'test.controller';
        $namedParam1      = 'name';
        $namedParam2      = 'phrase';
        $namedParam1Value = 'max-mensch';
        $namedParam2Value = 'hello-world';
        $routePathTest    = '/test-route/' . $namedParam1Value . '/' . $namedParam2Value;
        $routePathDefn    = '/test-route/{' . $namedParam1 . '}/{' . $namedParam2 . '}';
        $expected         = [
            '_route'      => $routeName,
            $namedParam1  => $namedParam1Value,
            $namedParam2  => $namedParam2Value,
            '_controller' => $controllerName
        ];

        $instance = $this->getInstance();

        $instance->getCollection()->add($routeName, new Route(
            $routePathDefn, [
                '_controller' => $controllerName
            ]
        ));

        $this->assertEquals($instance->matchRequest(Request::create($routePathTest)), $expected);
    }
}