<?php

use Maverick\Application,
    Maverick\Http\Router,
    Maverick\Http\Request,
    Maverick\Http\Response,
    Maverick\Http\Session,
    Maverick\DependencyManagement\ServiceManager;

class RouterTest extends PHPUnit_Framework_Testcase {
    private function getObj($req=null, $res=null) {
        $req      = $req ?: new Request();
        $session  = new Session();
        $res      = $res ?: new Response($req, $session);
        $services = new ServiceManager();

        return new Router($req, $res, $services);
    }

    public function testConstruct() {
        $obj = $this->getObj();

        $this->assertAttributeInstanceOf('Maverick\Http\Request', 'request', $obj);
        $this->assertAttributeInstanceOf('Maverick\Http\Response', 'response', $obj);
        $this->assertAttributeInstanceOf('Maverick\DependencyManagement\ServiceManager', 'services', $obj);
        $this->assertAttributeInstanceOf('Maverick\DataStructure\Map', 'named', $obj);
    }

    public function testUrlThatDoesNotMatch() {
        $obj = $this->getObj(new Request([
            'REQUEST_URI'    => '/path/to/pages',
            'REQUEST_METHOD' => 'POST'
        ]));

        $this->assertFalse($obj->match('GET', '/path/to/page', function () { }));
    }

    public function testRequestMethodRequirement() {
        $obj = $this->getObj(new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'POST'
        ]));

        $this->assertFalse($obj->match('GET', '/path/to/page', function () { }));
        $this->assertTrue($obj->match('POST', '/path/to/page', function () { }));
    }

    public function testRequestWithMultipleAllowedMethods() {
        $obj = $this->getObj(new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'POST'
        ]));

        $this->assertTrue($obj->match('GET|POST', '/path/to/page', function () { }));
    }

    public function testHttpRequirementWithHttps() {
        $obj = $this->getObj(new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'GET',
            'HTTPS'          => 'on'
        ]));

        $this->assertTrue($obj->match('GET', '/path/to/page', function () { }, ['https' => true]));
        $this->assertFalse($obj->match('GET', '/path/to/page', function () { }, ['https' => false]));
    }

    public function testHttpRequirementWithoutHttps() {
        $obj = $this->getObj(new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'GET'
        ]));

        $this->assertFalse($obj->match('GET', '/path/to/page', function () { }, ['https' => true]));
        $this->assertTrue($obj->match('GET', '/path/to/page', function () { }, ['https' => false]));
    }

    public function testStaticRoute() {
        $obj = $this->getObj(new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'GET'
        ]));

        $this->assertTrue($obj->match('GET', '/path/to/page', function () { }));
    }

    public function testRouteWithWildcardMethod() {
        $obj = $this->getObj(new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'POST'
        ]));

        $this->assertTrue($obj->match('*', '/path/to/page', function () { }));
    }

    public function testDynamicRoute() {
        $dynamicRoute = '/hello/{#([a-z]+)#i}';

        // This is good because we want to match "/hello/" then any number of alphabetic characters.
        $good = $this->getObj(new Request([
            'REQUEST_URI'    => '/hello/alec',
            'REQUEST_METHOD' => 'GET'
        ]));

        // This is bad because we need to have at least two parameters
        $badA = $this->getObj(new Request([
            'REQUEST_URI'    => '/hello',
            'REQUEST_METHOD' => 'GET'
        ]));

        // This is bad because we have too many parameters
        $badB = $this->getObj(new Request([
            'REQUEST_URI'    => '/hello/alec/carpenter',
            'REQUEST_METHOD' => 'GET'
        ]));

        // This is bad because 123 does not match the given regex
        $badC = $this->getObj(new Request([
            'REQUEST_URI'    => '/hello/123',
            'REQUEST_METHOD' => 'GET'
        ]));

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
        $res = new Response($req, new Session());
        $obj = $this->getObj($req, $res);
        
        $obj->match('GET', '/path/{#(\d+)#}/to/{#([a-z0-9]+)#i}/page/{#(\w+)#i}', function ($a, $b, $c) { return $a . $b . $c; });
        $obj->doRoute();

        $this->assertEquals('1234567890abc123abcdefg', $res->getBody());
    }

    public function testDynamicRouteWithRegexThatDoesNotMatchEntireParameter() {
        $req = new Request([
            'REQUEST_URI'    => '/regex/abc123',
            'REQUEST_METHOD' => 'GET'
        ]);
        $res = new Response($req, new Session());
        $obj = $this->getObj($req, $res);

        $obj->match('GET', '/regex/{#(\d+)#}', function ($str) { return $str; });
        $obj->doRoute();

        $this->assertEquals('123', $res->getBody());
    }

    public function testResponseGetsReturnValueFromController() {
        $returnVal = 'dynamicvalue';

        $req = new Request([
            'REQUEST_URI'    => '/path/to/page/' . $returnVal,
            'REQUEST_METHOD' => 'GET'
        ]);
        $res = new Response($req, new Session());
        $obj = $this->getObj($req, $res);

        $obj->match('GET', '/path/to/page/{#([a-z]+)#i}', function ($val) { return $val; });
        $obj->doRoute();

        $this->assertEquals($returnVal, $res->getBody());
    }

    public function testGetMethodRouteMethod() {
        $obj = $this->getObj(new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'GET'
        ]));

        $this->assertTrue($obj->get('/path/to/page', function () { }));
    }

    public function testPostMethodRouteMethod() {
        $obj = $this->getObj(new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'POST'
        ]));

        $this->assertTrue($obj->post('/path/to/page', function () { }));
    }

    public function testPutMethodRouteMethod() {
        $obj = $this->getObj(new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'PUT'
        ]));

        $this->assertTrue($obj->put('/path/to/page', function () { }));
    }

    public function testDeleteMethodRouteMethod() {
        $obj = $this->getObj(new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'DELETE'
        ]));

        $this->assertTrue($obj->delete('/path/to/page', function () { }));
    }

    public function testRouteDefinitionOrder() {
        $req = new Request([
            'REQUEST_URI'    => '/regex/abc123',
            'REQUEST_METHOD' => 'GET'
        ]);
        $res = new Response($req, new Session());
        $obj = $this->getObj($req, $res);

        $obj->match('GET', '/regex/{#([a-z]+)#}', function ($str) { return $str; });
        $obj->match('GET', '/regex/{#(\d+)#}', function ($str) { return $str; });
        $obj->doRoute();

        $this->assertEquals('abc', $res->getBody());
    }

    public function testResponseInstructionReturnedFromController() {
        $obj = $this->getObj(new Request([
            'REQUEST_URI'    => '/dev',
            'REQUEST_METHOD' => 'GET'
        ]));

        $mock = $this->getMockBuilder('Maverick\Http\Response\Instruction\RedirectInstruction')
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->once())
            ->method('instruct');

        $obj->match('get', '/dev', function () use($mock) {
            return $mock;
        });

        $obj->doRoute();
    }

    public function testAddNamedRoute() {
        $obj = $this->getObj();
        $urn = '/test';

        $obj->get($urn, 'callback', ['name' => 'testRoute']);

        $this->assertTrue($obj->getNamedRoutes()->has('testRoute'));
        $this->assertEquals($urn, $obj->getNamedRoutes()->get('testRoute'));
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testGenerateUrlThrowsExceptionWithInvalidNameType() {
        $obj = $this->getObj();

        $obj->get('/test', 'callback', ['name' => 'testRoute']);

        $obj->generateUrn(false);
    }

    /**
     * @expectedException Maverick\Exception\UnknownValueException
     */
    public function testGenerateUrlThrowsExceptionWithInvalidNameValue() {
        $obj = $this->getObj();

        $obj->get('/test', 'callback', ['name' => 'testRoute']);

        $obj->generateUrn('doesNotExist');
    }

    public function testGenerateUrlWithConstantUrn() {
        $obj = $this->getObj();
        $urn = '/test';

        $obj->get($urn, 'callback', ['name' => 'testRoute']);

        $this->assertEquals($urn, $obj->generateUrn('testRoute'));
    }

    /**
     * @expectedException Maverick\Exception\InvalidValueException
     */
    public function testGenerateUrlWithDynamicUrnThrowsExceptionWhithTooFewParams() {
        $obj = $this->getObj();

        $obj->get('/test/{#(\d+)#}', 'callback', ['name' => 'testRoute']);

        $obj->generateUrn('testRoute');
    }

    /**
     * @expectedException Maverick\Exception\InvalidValueException
     */
    public function testGenerateUrlWithDynamicUrnThrowsExceptionWhenRegexNotSatisfied() {
        $obj = $this->getObj();

        $obj->get('/test/{#(\d+)#}', 'callback', ['name' => 'testRoute']);

        $obj->generateUrn('testRoute', ['abc']);
    }

    public function testGenerateUrlWithDynamicUrn() {
        $obj = $this->getObj();
        $urn = '/test/{#(\d+)#}';

        $obj->get($urn, 'callback', ['name' => 'testRoute']);

        $this->assertEquals('/test/123', $obj->generateUrn('testRoute', ['123']));
    }

    public function testRouteOnlyAvailableInCertainEnvironment() {
        $urn = '/test/test-env-only';
        $obj = $this->getObj(new Request([
            'REQUEST_URI'    => $urn,
            'REQUEST_METHOD' => 'GET'
        ]));

        $mock = $this->getMock('stdClass', ['badAction', 'goodAction']);

        $mock->expects($this->never())
            ->method('badAction');

        $mock->expects($this->once())
            ->method('goodAction');

        $obj->get($urn, function() use($mock) {
            $mock->badAction();
        }, ['env' => Application::DEBUG_LEVEL_DEV]);

        $obj->get($urn, function() use($mock) {
            $mock->goodAction();
        }, ['env' => Application::getDebugLevel()]);

        $obj->doRoute();
    }
}