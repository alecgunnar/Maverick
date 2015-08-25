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
use Maverick\Http\StandardRequest;
use Maverick\Http\StandardResponse;
use Symfony\Component\Routing\Route;
use Maverick\Controller\ControllerInterface;
use Maverick\Router\Router;
use Symfony\Component\Routing\RouteCollection;
use Maverick\Collection\ControllerCollection;

class TestClassForContainer { }

class GoodController implements ControllerInterface
{
    public function doAction(StandardRequest $request) { }
}

/**
 * @covers Maverick\Maverick
 */
class MaverickTest extends PHPUnit_Framework_TestCase
{
    const CLASS_NAME = '\\Maverick\\Maverick';

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
            $request = StandardRequest::create('/');
        }

        if (!$response) {
            $response = StandardResponse::create();
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
        $request  = StandardRequest::create('/requested-uri');
        $instance = $this->getInstance(null, null, $request);

        $this->assertAttributeEquals($request, 'request', $instance);
    }

    /**
     * @covers Maverick\Maverick::__construct
     */
    public function testConstructorSetsResponse()
    {
        $response = StandardResponse::create();
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
        $request    = StandardRequest::create('/good-controller');
        $instance   = $this->getInstance(null, null, $request);
        $controller = $this->getGoodControllerMock($instance);

        $controller->expects($this->once())
            ->method('doAction')
            ->with($this->equalTo($request));

        $instance->getRouter()->getRoutes()->add('good-route', new Route('/good-controller', [
            '_controller' => 'good.controller'
        ]));

        $instance->run();
    }

    /**
     * @covers Maverick\Maverick::run
     */
    public function testRunAddsParamsToRequestAttributes()
    {
        $name = 'alec';
        $word = 'focus';

        $expected = [
            'name' => $name,
            'word' => $word
        ];

        $request    = StandardRequest::create('/good-controller-with-params/' . $name . '/' . $word);
        $instance   = $this->getInstance(null, null, $request);
        $controller = $this->getGoodControllerMock($instance);

        $instance->getRouter()->getRoutes()->add('good-route-with-params', new Route('/good-controller-with-params/{name}/{word}', [
            '_controller' => 'good.controller'
        ]));

        $instance->run();

        $this->assertEquals($request->attributes->all(), $expected);
    }

    /**
     * @covers Maverick\Maverick::run
     */
    public function testRunSendsResponseReturnValue()
    {
        $request    = StandardRequest::create('/good-controller');
        $response   = $this->getMockBuilder('Maverick\\Http\\StandardResponse')->getMock();
        $instance   = $this->getInstance(null, null, $request, $response);
        $controller = $this->getGoodControllerMock($instance);

        $response->expects($this->once())
            ->method('send')
            ->will($this->returnValue($response));

        $controller->method('doAction')
            ->will($this->returnValue($response));

        $instance->getRouter()->getRoutes()->add('good-route', new Route('/good-controller', [
            '_controller' => 'good.controller'
        ]));

        $this->assertEquals($instance->run(), $response);
    }

    /**
     * @covers Maverick\Maverick::getRequest
     */
    public function testGetRouter()
    {
        $router   = new Router(new RouteCollection(), new ControllerCollection());
        $instance = $this->getInstance(null, $router);

        $this->assertEquals($instance->getRouter(), $router);
    }

    /**
     * @covers Maverick\Maverick::getRequest
     */
    public function testGetRequest()
    {
        $request  = StandardRequest::createFromGlobals();
        $instance = $this->getInstance(null, null, $request);

        $this->assertEquals($instance->getRequest(), $request);
    }

    /**
     * @covers Maverick\Maverick::getResponse
     */
    public function testGetResponse()
    {
        $response = StandardResponse::create();
        $instance = $this->getInstance(null, null, null, $response);

        $this->assertEquals($instance->getResponse(), $response);
    }
}
