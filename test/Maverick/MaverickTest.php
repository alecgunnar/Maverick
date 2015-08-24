<?php
/**
 * Maverick
 *
 * @author Alec Carpenter <gunnar94@me.com>
 */

use Maverick\Maverick;
use Symfony\Component\Config\FileLocator;
use Maverick\Loader\YamlConfigLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Maverick\Controller\ControllerInterface;

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

    protected function getInstance($configLoader=null, $request=null, $response=null)
    {
        return new Maverick($configLoader ?: new YamlConfigLoader(new FileLocator([
            TEST_PATH . DIRECTORY_SEPARATOR . 'config'
        ])), $request, $response);
    }

    public function testForClassAttributes()
    {
        $this->getInstance();
        $this->assertClassHasAttribute('config', self::CLASS_NAME);
        $this->assertClassHasAttribute('container', self::CLASS_NAME);
        $this->assertClassHasAttribute('routes', self::CLASS_NAME);
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
    public function testConstructorSetsRequest()
    {
        $request  = Request::create('/requested-uri');
        $instance = $this->getInstance(null, $request);

        $this->assertAttributeEquals($request, 'request', $instance);
    }

    /**
     * @covers Maverick\Maverick::__construct
     */
    public function testConstructorSetsResponse()
    {
        $response = Response::create();
        $instance = $this->getInstance(null, null, $response);

        $this->assertAttributeEquals($response, 'response', $instance);
    }

    /**
     * @covers Maverick\Maverick::__construct
     */
    public function testConstructorCreatesContainer()
    {
        $instance = $this->getInstance();

        $this->assertAttributeInstanceOf('Symfony\\Component\\DependencyInjection\\ContainerBuilder', 'container', $instance);
    }

    /**
     * @covers Maverick\Maverick::__construct
     */
    public function testConstructorCreatesRequest()
    {
        $instance = $this->getInstance();

        $this->assertAttributeInstanceOf('Symfony\\Component\\HttpFoundation\\Request', 'request', $instance);
    }

    /**
     * @covers Maverick\Maverick::__construct
     */
    public function testConstructorCreatesResponse()
    {
        $instance = $this->getInstance();

        $this->assertAttributeInstanceOf('Symfony\\Component\\HttpFoundation\\Response', 'response', $instance);
    }

    /**
     * @covers Maverick\Maverick::__construct
     */
    public function testConstructorCreatesContainerWithServices()
    {
        $instance = $this->getInstance();

        $this->assertTrue($instance->getContainer()->get('test.class.for.container') instanceof TestClassForContainer);
    }

    /**
     * @covers Maverick\Maverick::loadContainer
     */
    public function testFrameworkServiceConfigLoaded()
    {
        $instance = $this->getInstance();

        $this->assertTrue($instance->getContainer()->has('maverick.stdclass'));
    }

    /**
     * @covers Maverick\Maverick::loadFrameworkServices
     */
    public function testApplicationServicesOverrideFrameworkServices()
    {
        $instance = $this->getInstance();

        $this->assertTrue($instance->getContainer()->get('maverick.stdclass') instanceof \DateTime);
    }

    /**
     * @covers Maverick\Maverick::loadRoutes
     */
    public function testRoutesLoaded()
    {
        $instance = $this->getInstance();

        $this->assertTrue($instance->getRoutes()->get('homepage') instanceof Route);
    }

    /**
     * @covers Maverick\Maverick::run
     * @expectedException Maverick\Exception\NoControllerException
     */
    public function testRunExpectsController()
    {
        $request  = Request::create('/no-controller');
        $instance = $this->getInstance(null, $request);

        $instance->run();
    }

    /**
     * @covers Maverick\Maverick::run
     * @expectedException Maverick\Exception\UndefinedControllerException
     */
    public function testRunExpectsControllerDefinedInContainer()
    {
        $request  = Request::create('/undefined-controller');
        $instance = $this->getInstance(null, $request);

        $instance->run();
    }

    /**
     * @covers Maverick\Maverick::run
     * @expectedException Maverick\Exception\InvalidControllerException
     */
    public function testRunExpectsProperlyImplementedController()
    {
        $request  = Request::create('/bad-controller');
        $instance = $this->getInstance(null, $request);

        $instance->run();
    }

    public function getGoodControllerMock($instance)
    {
        $controller = $this->getMockBuilder('GoodController')->getMock();
        $instance->getContainer()->set('good.controller', $controller);
        return $controller;
    }

    /**
     * @covers Maverick\Maverick::run
     */
    public function testRunCallsMatchedRoutesController()
    {
        $request    = Request::create('/good-controller');
        $instance   = $this->getInstance(null, $request);
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
        $instance   = $this->getInstance(null, $request);
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
    public function testRunAddsStringReturnValueToDefaultResponse()
    {
        $output   = 'This is a test!';
        $request  = Request::create('/good-controller');
        $response = $this->getMockBuilder('Symfony\\Component\\HttpFoundation\\Response')->getMock();

        $response->expects($this->once())
            ->method('setContent')
            ->with($this->equalTo($output))
            ->will($this->returnValue($response));

        $response->expects($this->once())
            ->method('send')
            ->will($this->returnValue($response));

        $instance   = $this->getInstance(null, $request, $response);
        $controller = $this->getGoodControllerMock($instance);

        $controller->method('doAction')
            ->will($this->returnValue($output));

        $this->assertEquals($instance->run(), $response);
    }

    /**
     * @covers Maverick\Maverick::run
     */
    public function testRunSendsResponseReturnValue()
    {
        $request    = Request::create('/good-controller');
        $instance   = $this->getInstance(null, $request);
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
        $instance = $this->getInstance(null, $request);

        $this->assertEquals($instance->getRequest(), $request);
    }

    /**
     * @covers Maverick\Maverick::getResponse
     */
    public function testGetResponse()
    {
        $response = Response::create();
        $instance = $this->getInstance(null, null, $response);

        $this->assertEquals($instance->getResponse(), $response);
    }
}
