<?php

use Maverick\Http\Router,
    Maverick\Http\Request,
    Maverick\Http\Response,
    Maverick\Http\Session,
    Maverick\DependencyManagement\ServiceManager;

class RouterTest extends PHPUnit_Framework_Testcase {
    public function testConstruct() {
        $req      = new Request();
        $session  = new Session();
        $res      = new Response($req, $session);
        $services = new ServiceManager();
        $obj      = new Router($req, $res, $services);

        $this->assertAttributeInstanceOf('Maverick\Http\Request', 'request', $obj);
        $this->assertAttributeInstanceOf('Maverick\Http\Response', 'response', $obj);
        $this->assertAttributeInstanceOf('Maverick\DependencyManagement\ServiceManager', 'services', $obj);
    }

    public function testUrlThatDoesNotMatch() {
        $req = new Request([
            'REQUEST_URI'    => '/path/to/pages',
            'REQUEST_METHOD' => 'POST'
        ]);
        $session  = new Session();
        $res      = new Response($req, $session);
        $services = new ServiceManager();
        $obj      = new Router($req, $res, $services);

        $this->assertFalse($obj->match('GET', '/path/to/page', function () { }));
    }

    public function testRequestMethodRequirement() {
        $req = new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'POST'
        ]);
        $session  = new Session();
        $res      = new Response($req, $session);
        $services = new ServiceManager();
        $obj      = new Router($req, $res, $services);

        $this->assertFalse($obj->match('GET', '/path/to/page', function () { }));
        $this->assertTrue($obj->match('POST', '/path/to/page', function () { }));
    }

    public function testRequestWithMultipleAllowedMethods() {
        $req = new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'POST'
        ]);
        $session  = new Session();
        $res      = new Response($req, $session);
        $services = new ServiceManager();
        $obj      = new Router($req, $res, $services);

        $this->assertTrue($obj->match('GET|POST', '/path/to/page', function () { }));
    }

    public function testHttpRequirementWithHttps() {
        $req = new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'GET',
            'HTTPS'          => 'on'
        ]);

        $session  = new Session();
        $res      = new Response($req, $session);
        $services = new ServiceManager();
        $obj      = new Router($req, $res, $services);

        $this->assertTrue($obj->match('GET', '/path/to/page', function () { }, ['https' => true]));
        $this->assertFalse($obj->match('GET', '/path/to/page', function () { }, ['https' => false]));
    }

    public function testHttpRequirementWithoutHttps() {
        $req = new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'GET'
        ]);

        $session = new Session();
        $res     = new Response($req, $session);
        $services = new ServiceManager();
        $router   = new Router($req, $res, $services);

        $this->assertFalse($router->match('GET', '/path/to/page', function () { }, ['https' => true]));
        $this->assertTrue($router->match('GET', '/path/to/page', function () { }, ['https' => false]));
    }

    public function testStaticRoute() {
        $req = new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'GET'
        ]);
        $session  = new Session();
        $res      = new Response($req, $session);
        $services = new ServiceManager();
        $obj      = new Router($req, $res, $services);

        $this->assertTrue($obj->match('GET', '/path/to/page', function () { }));
    }

    public function testRouteWithWildcardMethod() {
        $req = new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'POST'
        ]);
        $session  = new Session();
        $res      = new Response($req, $session);
        $services = new ServiceManager();
        $obj      = new Router($req, $res, $services);

        $this->assertTrue($obj->match('*', '/path/to/page', function () { }));
    }

    public function testDynamicRoute() {
        $dynamicRoute = '/hello/{#([a-z]+)#i}';

        $res      = new Response(new Request(), new Session());
        $services = new ServiceManager();
        // This is good because we want to match "/hello/" then any number of alphabetic characters.
        $good = new Router(new Request([
            'REQUEST_URI'    => '/hello/alec',
            'REQUEST_METHOD' => 'GET'
        ]), $res, $services);

        // This is bad because we need to have at least two parameters
        $badA = new Router(new Request([
            'REQUEST_URI'    => '/hello',
            'REQUEST_METHOD' => 'GET'
        ]), $res, $services);

        // This is bad because we have too many parameters
        $badB = new Router(new Request([
            'REQUEST_URI'    => '/hello/alec/carpenter',
            'REQUEST_METHOD' => 'GET'
        ]), $res, $services);

        // This is bad because 123 does not match the given regex
        $badC = new Router(new Request([
            'REQUEST_URI'    => '/hello/123',
            'REQUEST_METHOD' => 'GET'
        ]), $res, $services);

        $this->assertTrue($good->match('GET', $dynamicRoute, function ($name) { }));
        $this->assertFalse($badA->match('GET', $dynamicRoute, function ($name) { }));
        $this->assertFalse($badB->match('GET', $dynamicRoute, function ($name) { }));
        $this->assertFalse($badC->match('GET', $dynamicRoute, function ($name) { }));
    }

    public function testDynamicRouteWithMultipleDynamicParameters() {
        $req = new Request([
            'REQUEST_URI'    => '/path/1234567890/to/abc123/page/abcdefg',
            'REQUEST_METHOD' => 'GET'
        ]);
        $session  = new Session();
        $res      = new Response($req, $session);
        $services = new ServiceManager();
        $obj      = new Router($req, $res, $services);

        $this->assertTrue($obj->match('GET', '/path/{#(\d+)#}/to/{#([a-z0-9]+)#i}/page/{#(\w+)#i}', function ($a, $b, $c) { return $a . $b . $c; }));
        $this->assertEquals('1234567890abc123abcdefg', $res->getBody());
    }

    public function testDynamicRouteWithRegexThatDoesNotMatchEntireParameter() {
        $req = new Request([
            'REQUEST_URI'    => '/regex/abc123',
            'REQUEST_METHOD' => 'GET'
        ]);
        $session  = new Session();
        $res      = new Response($req, $session);
        $services = new ServiceManager();
        $obj      = new Router($req, $res, $services);

        $obj->match('GET', '/regex/{#(\d+)#}', function ($str) { return $str; });

        $this->assertEquals('123', $res->getBody());
    }

    public function testResponseGetsReturnValueFromController() {
        $returnVal = 'dynamicvalue';

        $req = new Request([
            'REQUEST_URI'    => '/path/to/page/' . $returnVal,
            'REQUEST_METHOD' => 'GET'
        ]);
        $session  = new Session();
        $res      = new Response($req, $session);
        $services = new ServiceManager();
        $obj      = new Router($req, $res, $services);

        $obj->match('GET', '/path/to/page/{#([a-z]+)#i}', function ($val) { return $val; });

        $this->assertEquals($returnVal, $res->getBody());
    }

    public function testGetMethodRouteMethod() {
        $req = new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'GET'
        ]);
        $session  = new Session();
        $res      = new Response($req, $session);
        $services = new ServiceManager();
        $obj      = new Router($req, $res, $services);

        $this->assertTrue($obj->get('/path/to/page', function () { }));
    }

    public function testPostMethodRouteMethod() {
        $req = new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'POST'
        ]);
        $session  = new Session();
        $res      = new Response($req, $session);
        $services = new ServiceManager();
        $obj      = new Router($req, $res, $services);

        $this->assertTrue($obj->post('/path/to/page', function () { }));
    }

    public function testPutMethodRouteMethod() {
        $req = new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'PUT'
        ]);
        $session  = new Session();
        $res      = new Response($req, $session);
        $services = new ServiceManager();
        $obj      = new Router($req, $res, $services);

        $this->assertTrue($obj->put('/path/to/page', function () { }));
    }

    public function testDeleteMethodRouteMethod() {
        $req = new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'DELETE'
        ]);
        $session  = new Session();
        $res      = new Response($req, $session);
        $services = new ServiceManager();
        $obj      = new Router($req, $res, $services);

        $this->assertTrue($obj->delete('/path/to/page', function () { }));
    }

    public function testRouteDefinitionOrder() {
        $req = new Request([
            'REQUEST_URI'    => '/regex/abc123',
            'REQUEST_METHOD' => 'GET'
        ]);
        $session  = new Session();
        $res      = new Response($req, $session);
        $services = new ServiceManager();
        $obj      = new Router($req, $res, $services);

        $obj->match('GET', '/regex/{#([a-z]+)#}', function ($str) { return $str; });
        $obj->match('GET', '/regex/{#(\d+)#}', function ($str) { return $str; });

        $this->assertEquals('abc', $res->getBody());
    }

    public function testResponseInstructionReturnedFromController() {
        $req = new Request([
            'REQUEST_URI'    => '/dev',
            'REQUEST_METHOD' => 'GET'
        ]);
        $session  = new Session();
        $res      = new Response($req, $session);
        $services = new ServiceManager();
        $obj      = new Router($req, $res, $services);

        $mock = $this->getMockBuilder('Maverick\Http\Response\Instruction\RedirectInstruction')
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->once())
            ->method('instruct');

        $obj->match('get', '/dev', function () use($mock) {
            return $mock;
        });
    }
}