<?php
/**
 * Maverick
 *
 * @package Maverick
 * @author  Alec Carpenter <gunnar94@me.com>
 */

use Maverick\Router\Router;
use Maverick\Collection\ControllerCollection;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Maverick\Http\StandardResponse;
use Maverick\Http\StandardRequest;

/**
 * @covers Maverick\Router\Router
 */
class RouterTest extends PHPUnit_Framework_TestCase
{
    const CLASS_NAME = '\\Maverick\\Router\\Router';

    protected function getInstance($routes=null, $controllers=null)
    {
        return new Router(
            $routes ?: new RouteCollection(),
            $controllers ?: new ControllerCollection()
        );
    }

    public function testForClassAttributes()
    {
        $this->getInstance();
        $this->assertClassHasAttribute('routes', self::CLASS_NAME);
        $this->assertClassHasAttribute('controllers', self::CLASS_NAME);
    }

    /**
     * @covers Maverick\Router\Router::__construct
     */
    public function testConstructorSetsAttributes()
    {
        $routes      = new RouteCollection();
        $controllers = new ControllerCollection();
        $instance    = new Router($routes, $controllers);

        $this->assertAttributeEquals($routes, 'routes', $instance);
        $this->assertAttributeEquals($controllers, 'controllers', $instance);
    }

    /**
     * @covers Maverick\Router\Router::getRoutes
     */
    public function testGetRoutes()
    {
        $routes   = new RouteCollection();
        $instance = $this->getInstance($routes);

        $this->assertEquals($instance->getRoutes(), $routes);
    }

    /**
     * @covers Maverick\Router\Router::getControllers
     */
    public function testGetControllers()
    {
        $controllers = new ControllerCollection();
        $instance    = $this->getInstance(null, $controllers);

        $this->assertEquals($instance->getControllers(), $controllers);
    }

    /**
     * @covers Maverick\Router\Router::matchRequest
     */
    public function testMatchRequestWithDefinedRoute()
    {
        $controller     = new TestController(StandardResponse::create());
        $routeName      = 'test-route';
        $controllerName = 'test.controller';
        $routePath      = '/test-route';
        $expected       = [
            '_route'      => $routeName,
            '_controller' => $controller
        ];

        $controllers = new ControllerCollection();
        $controllers->add($controllerName, $controller);

        $instance = $this->getInstance(null, $controllers);

        $instance->getRoutes()->add($routeName, new Route(
            $routePath, [
                '_controller' => $controllerName
            ]
        ));

        $this->assertEquals($instance->matchRequest(StandardRequest::create($routePath)), $expected);
    }

    /**
     * @covers Maverick\Router\Router::matchRequest
     * @expectedException Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function testMatchRequestWithUndefinedRoute()
    {
        $this->getInstance()->matchRequest(StandardRequest::create('/undefined-route'));
    }

    /**
     * @covers Maverick\Router\Router::matchRequest
     */
    public function testMatchRequestWithDefinedRouteAndParams()
    {
        $controller       = new TestController(StandardResponse::create());
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
            '_controller' => $controller
        ];

        $controllers = new ControllerCollection();
        $controllers->add($controllerName, $controller);

        $instance = $this->getInstance(null, $controllers);

        $instance->getRoutes()->add($routeName, new Route(
            $routePathDefn, [
                '_controller' => $controllerName
            ]
        ));

        $this->assertEquals($instance->matchRequest(StandardRequest::create($routePathTest)), $expected);
    }

    /**
     * @covers Maverick\Router\Router::matchRequest
     * @expectedException Maverick\Router\Exception\NoControllerException
     */
    public function testMatchRequestWithoutControllerDefinedByRoute()
    {
        $routePath = '/test-route';
        $instance  = $this->getInstance();

        $instance->getRoutes()->add('test-route', new Route($routePath));

        $instance->matchRequest(StandardRequest::create($routePath));
    }

    /**
     * @covers Maverick\Router\Router::matchRequest
     * @expectedException Maverick\Router\Exception\UndefinedControllerException
     */
    public function testMatchRequestWithUndefinedController()
    {
        $routePath = '/test-route';
        $instance  = $this->getInstance();

        $instance->getRoutes()->add('test-route', new Route($routePath, [
            '_controller' => 'undefined.controller'
        ]));

        $instance->matchRequest(StandardRequest::create($routePath));
    }
}