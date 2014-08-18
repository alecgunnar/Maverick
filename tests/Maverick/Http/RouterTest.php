<?php

use Maverick\Http\Router,
    Maverick\Http\Request,
    Maverick\Http\Response;

class RouterTest extends PHPUnit_Framework_Testcase {
    public function testConstruct() {
        $req = new Request;
        $res = new Response($req);
        $obj = new Router($req, $res);

        $this->assertAttributeInstanceOf('Maverick\Http\Request', 'request', $obj);
        $this->assertAttributeInstanceOf('Maverick\Http\Response', 'response', $obj);
    }

    public function testRequestMethodRequirement() {
        $req = new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'POST'
        ]);
        $res = new Response($req);
        $obj = new Router($req, $res);

        $this->assertFalse($obj->match('GET', '/path/to/page', function () { }));
        $this->assertTrue($obj->match('POST', '/path/to/page', function () { }));
    }

    public function testRequestWithMultipleAllowedMethods() {
        $req = new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'POST'
        ]);
        $res = new Response($req);
        $obj = new Router($req, $res);

        $this->assertTrue($obj->match('GET|POST', '/path/to/page', function () { }));
    }

    public function testHttpRequirement() {
        $req = new Request;
        $res = new Response($req);

        $http = new Router(new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'GET'
        ]), $res);

        $https = new Router(new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'GET',
            'HTTPS'          => 'on'
        ]), $res);

        $this->assertTrue($https->match('GET', '/path/to/page', function () { }, ['https' => true]));
        $this->assertFalse($http->match('GET', '/path/to/page', function () { }, ['https' => true]));

        $this->assertTrue($http->match('GET', '/path/to/page', function () { }, ['https' => false]));
        $this->assertFalse($https->match('GET', '/path/to/page', function () { }, ['https' => false]));
    }

    public function testStaticRoute() {
        $req = new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'GET'
        ]);
        $res = new Response($req);
        $obj = new Router($req, $res);

        $this->assertTrue($obj->match('GET', '/path/to/page', function () { }));
    }

    public function testRouteWithWildcardMethod() {
        $req = new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'POST'
        ]);
        $res = new Response($req);
        $obj = new Router($req, $res);

        $this->assertTrue($obj->match('*', '/path/to/page', function () { }));
    }

    public function testDynamicRoute() {
        $dynamicRoute = '/hello/{#([a-z]+)#i}';

        $req = new Request;
        $res = new Response($req);

        // This is good because we want to match "/hello/" then any number of alphabetic characters.
        $good = new Router(new Request([
            'REQUEST_URI'    => '/hello/alec',
            'REQUEST_METHOD' => 'GET'
        ]), $res);

        // This is bad because we need to have at least two parameters
        $badA = new Router(new Request([
            'REQUEST_URI'    => '/hello',
            'REQUEST_METHOD' => 'GET'
        ]), $res);

        // This is bad because we have too many parameters
        $badB = new Router(new Request([
            'REQUEST_URI'    => '/hello/alec/carpenter',
            'REQUEST_METHOD' => 'GET'
        ]), $res);

        // This is bad because 123 does not match the given regex
        $badC = new Router(new Request([
            'REQUEST_URI'    => '/hello/123',
            'REQUEST_METHOD' => 'GET'
        ]), $res);

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
        $res = new Response($req);
        $obj = new Router($req, $res);

        $this->assertTrue($obj->match('GET', '/path/{#(\d+)#}/to/{#([a-z0-9]+)#i}/page/{#(\w+)#i}', function ($a, $b, $c) { return $a . $b . $c; }));
        $this->assertEquals('1234567890abc123abcdefg', $res->getBody());
    }

    public function testDynamicRouteWithRegexThatDoesNotMatchEntireParameter() {
        $req = new Request([
            'REQUEST_URI'    => '/regex/abc123',
            'REQUEST_METHOD' => 'GET'
        ]);
        $res = new Response($req);
        $obj = new Router($req, $res);

        $obj->match('GET', '/regex/{#(\d+)#}', function ($str) { return $str; });

        $this->assertEquals('123', $res->getBody());
    }

    public function testResponseGetsReturnValueFromController() {
        $returnVal = 'dynamicvalue';

        $req = new Request([
            'REQUEST_URI'    => '/path/to/page/' . $returnVal,
            'REQUEST_METHOD' => 'GET'
        ]);
        $res = new Response($req);
        $obj = new Router($req, $res);

        $obj->match('GET', '/path/to/page/{#([a-z]+)#i}', function ($val) { return $val; });

        $this->assertEquals($returnVal, $res->getBody());
    }

    public function testGetMethodRouteMethod() {
        $req = new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'GET'
        ]);
        $res = new Response($req);
        $obj = new Router($req, $res);

        $this->assertTrue($obj->get('/path/to/page', function () { }));
    }

    public function testPostMethodRouteMethod() {
        $req = new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'POST'
        ]);
        $res = new Response($req);
        $obj = new Router($req, $res);

        $this->assertTrue($obj->post('/path/to/page', function () { }));
    }

    public function testPutMethodRouteMethod() {
        $req = new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'PUT'
        ]);
        $res = new Response($req);
        $obj = new Router($req, $res);

        $this->assertTrue($obj->put('/path/to/page', function () { }));
    }

    public function testDeleteMethodRouteMethod() {
        $req = new Request([
            'REQUEST_URI'    => '/path/to/page',
            'REQUEST_METHOD' => 'DELETE'
        ]);
        $res = new Response($req);
        $obj = new Router($req, $res);

        $this->assertTrue($obj->delete('/path/to/page', function () { }));
    }

    public function testRouteDefinitionOrder() {
        $req = new Request([
            'REQUEST_URI'    => '/regex/abc123',
            'REQUEST_METHOD' => 'GET'
        ]);
        $res = new Response($req);
        $obj = new Router($req, $res);

        $obj->match('GET', '/regex/{#([a-z]+)#}', function ($str) { return $str; });
        $obj->match('GET', '/regex/{#(\d+)#}', function ($str) { return $str; });

        $this->assertEquals('abc', $res->getBody());
    }
}