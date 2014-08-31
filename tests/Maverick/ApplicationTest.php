<?php

use Maverick\Application;

class ApplicationTest extends PHPUnit_Framework_TestCase {
    public function __construct() {
        ini_set('display_errors', true);
    }

    public function testConstructAndStart() {
        $app = new Application;
        $app->start();

        $this->assertAttributeInstanceOf('Maverick\Http\Request', 'request', $app);

        $this->assertAttributeInstanceOf('Maverick\Http\Router', 'router', $app);
        $this->assertAttributeEquals($app->request, 'request', $app->router);
        $this->assertAttributeEquals($app->response, 'response', $app->router);

        $this->assertAttributeInstanceOf('Maverick\Http\Response', 'response', $app);
        $this->assertAttributeEquals($app->request, 'request', $app->response);

        $this->assertAttributeInstanceOf('Maverick\Http\Session', 'session', $app);
        $this->assertAttributeInstanceOf('Maverick\DependencyManagement\ServiceManager', 'services', $app);
    }

    /**
     * @expectedException Maverick\Exception\NoRouteException
     */
    public function testFinishWithNoRouteMatched() {
        $app = new Application;
        $app->start();
        $app->finish();
    }

    /**
     * @expectedException Maverick\Exception\InvalidValueException
     */
    public function testSetDebugLevelWithInvalidLevel() {
        Application::setDebugLevel(123);
    }

    public function testDebugCompare() {
        Application::setDebugLevel(Application::DEBUG_LEVEL_TEST);
        $this->assertTrue(Application::debugCompare('>', Application::DEBUG_LEVEL_DEV));
        $this->assertFalse(Application::debugCompare('<', Application::DEBUG_LEVEL_DEV));
        $this->assertTrue(Application::debugCompare('<=', Application::DEBUG_LEVEL_TEST));
        $this->assertTrue(Application::debugCompare('>=', Application::DEBUG_LEVEL_DEV));
        $this->assertTrue(Application::debugCompare('==', Application::DEBUG_LEVEL_TEST));
        $this->assertTrue(Application::debugCompare('===', Application::DEBUG_LEVEL_TEST));
        $this->assertTrue(Application::debugCompare('!=', Application::DEBUG_LEVEL_DEV));
    }

    /**
     * @expectedException Maverick\Exception\InvalidValueException
     */
    public function testDebugCompareWithInvalidOperator() {
        $this->assertTrue(Application::debugCompare('=>', Application::DEBUG_LEVEL_DEV));
    }

    public function testGetConfig() {
        $this->assertFalse(Application::getConfig('system')->get('expose_maverick'));
    }
}