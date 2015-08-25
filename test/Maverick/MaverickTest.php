<?php
/**
 * Maverick
 *
 * @package Maverick
 * @author  Alec Carpenter <gunnar94@me.com>
 */

use Maverick\Maverick;
use Symfony\Component\Config\FileLocator;
use Maverick\Loader\YamlConfigLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Maverick\Controller\ControllerInterface;
use Maverick\Router\Router;
use Symfony\Component\Routing\RouteCollection;
use Maverick\Collection\ControllerCollection;

class TestClassForContainer { }

class GoodController implements ControllerInterface
{
    public function doAction() { }
}

/**
 * @covers Maverick\Maverick
 */
class MaverickTest extends PHPUnit_Framework_TestCase
{
    const CLASS_NAME = '\\Maverick\\Maverick';

    private $container;

    protected function getInstance($loader=null, $router=null, $request=null, $response=null)
    {
        if (!$loader) {
            $loader = new YamlConfigLoader(new FileLocator([
                TEST_PATH . DIRECTORY_SEPARATOR . 'config'
            ]));
        }

        if (!$router) {
            $router = new Router(new RouteCollection(), new ControllerCollection());
        }

        if (!$request) {
            $request = Request::create('/');
        }

        if (!$response) {
            $response = Response::create();
        }

        return new Maverick($loader, $router, $request, $response);
    }

    public function testForClassAttributes()
    {
        $this->getInstance();
        $this->assertClassHasAttribute('config', self::CLASS_NAME);
        $this->assertClassHasAttribute('router', self::CLASS_NAME);
        $this->assertClassHasAttribute('request', self::CLASS_NAME);
        $this->assertClassHasAttribute('response', self::CLASS_NAME);
    }

    /**
     * @covers Maverick\Maverick::__construct
     */
    public function testConstructorSetsConfigLoader()
    {
        $loader = new YamlConfigLoader(new FileLocator([
            TEST_PATH . DIRECTORY_SEPARATOR . 'config'
        ]));

        $instance = $this->getInstance($loader);

        $this->assertAttributeEquals($loader, 'config', $instance);
    }

    /**
     * @covers Maverick\Maverick::__construct
     */
    public function testConstructorSetsRouter()
    {
        $router   = new Router(new RouteCollection(), new ControllerCollection());
        $instance = $this->getInstance(null, $router);

        $this->assertAttributeEquals($router, 'router', $instance);
    }

    /**
     * @covers Maverick\Maverick::__construct
     */
    public function testConstructorSetsRequest()
    {
        $request  = Request::create('/requested-uri');
        $instance = $this->getInstance(null, null, $request);

        $this->assertAttributeEquals($request, 'request', $instance);
    }

    /**
     * @covers Maverick\Maverick::__construct
     */
    public function testConstructorSetsResponse()
    {
        $response = Response::create();
        $instance = $this->getInstance(null, null, null, $response);

        $this->assertAttributeEquals($response, 'response', $instance);
    }

    /**
     * @covers Maverick\Maverick::loadRoutes
     */
    public function testRoutesLoaded()
    {
        $collection = $this->getMockBuilder('Symfony\\Component\\Routing\\RouteCollection')->getMock();

        $collection->expects($this->once())
            ->method('addCollection')
            ->with($this->isInstanceOf('Symfony\\Component\\Routing\\RouteCollection'));

        $router   = new Router($collection, new ControllerCollection());
        $instance = $this->getInstance(null, $router);
    }

    /**
     * @covers Maverick\Maverick::run
     */
    public function testNotFoundControllerRunWhenNoRouteMatched()
    {
        $request  = Request::create('/not-found');
        $instance = $this->getInstance(null, null, $request);

        $controller = $this->getMockBuilder('Maverick\\Controller\\NotFoundController')->getMock();

        $controller->expects($this->once())
            ->method('doAction');

        $instance->getRouter()->getControllers()->add('maverick.controller.not_found', $controller);

        $instance->run();
    }

    public function getGoodControllerMock($instance)
    {
        $controller = $this->getMockBuilder('GoodController')->getMock();
        $instance->getRouter()->getControllers()->add('good.controller', $controller);
        return $controller;
    }

    /**
     * @covers Maverick\Maverick::run
     */
    public function testRunCallsMatchedRoutesController()
    {
        $request    = Request::create('/good-controller');
        $instance   = $this->getInstance(null, null, $request);
        $controller = $this->getGoodControllerMock($instance);

        $controller->expects($this->once())
            ->method('doAction');

        $instance->run();
    }

    /**
     * @covers Maverick\Maverick::run
     */
    public function testRunCallsMatchedRoutesControllerWithParams()
    {
        $name = 'alec';
        $word = 'focus';

        $request    = Request::create('/good-controller-with-params/' . $name . '/' . $word);
        $instance   = $this->getInstance(null, null, $request);
        $controller = $this->getGoodControllerMock($instance);

        $controller->expects($this->once())
            ->method('doAction')
            ->with(
                $this->equalTo($name),
                $this->equalTo($word)
            );

        $instance->run();
    }

    /**
     * @covers Maverick\Maverick::run
     */
    public function testRunSendsResponseReturnValue()
    {
        $request    = Request::create('/good-controller');
        $instance   = $this->getInstance(null, null, $request);
        $controller = $this->getGoodControllerMock($instance);
        $response   = $this->getMockBuilder('Symfony\\Component\\HttpFoundation\\Response')->getMock();

        $response->expects($this->once())
            ->method('send')
            ->will($this->returnValue($response));

        $controller->method('doAction')
            ->will($this->returnValue($response));

        $this->assertEquals($instance->run(), $response);
    }

    /**
     * @covers Maverick\Maverick::getRequest
     */
    public function testGetRequest()
    {
        $request  = Request::createFromGlobals();
        $instance = $this->getInstance(null, null, $request);

        $this->assertEquals($instance->getRequest(), $request);
    }

    /**
     * @covers Maverick\Maverick::getResponse
     */
    public function testGetResponse()
    {
        $response = Response::create();
        $instance = $this->getInstance(null, null, null, $response);

        $this->assertEquals($instance->getResponse(), $response);
    }
}
