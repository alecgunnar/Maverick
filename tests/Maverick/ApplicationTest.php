<?php

use Maverick\Application;

class ApplicationTest extends PHPUnit_Framework_TestCase {
    public function testConstruct() {
        $app = new Application;

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
        $app->finish();
    }

    public function testIniSetShowErrorsForTesting() {
        ini_set('display_errors', true);
    }
}