<?php

use Maverick\Http\Request;

class RequestTest extends PHPUnit_Framework_TestCase {
    public function testConstruct() {
        $url = 'www.example.com';

        $_GET = $get = [
            'abc' => 'def',
            'ghj' => 'jkl'
        ];

        $_POST = $post = [
            'mno' => 'pqr',
            'stu' => 'vwx'
        ];

        $headers = [
            'HTTP_ACCEPTS'    => 'abc',
            'HTTP_MULTI_WORD' => 'def'
        ];

        $headersCleaned = [
            'accepts'    => 'abc',
            'multi_word' => 'def'
        ];

        $vars = [
            'HOST'           => 'maverick',
            'REQUEST_METHOD' => 'GET',
            'SERVER_NAME'    => 'www.example.com/',
            'REQUEST_URI'    => '/name/of/page/',
            'TEST'           => 'value'
        ];

        $varsCleaned = [
            'host' => 'maverick',
            'test' => 'value'
        ];

        $obj = new Request($headers + $vars);

        $this->assertAttributeInstanceOf('Maverick\DataStructure\ReadOnlyMap', 'headers', $obj);
        $this->assertEquals($headersCleaned, $obj->getHeaders()->dump());
        $this->assertAttributeInstanceOf('Maverick\DataStructure\ReadOnlyMap', 'env', $obj);
        $this->assertEquals($varsCleaned, $obj->getEnv()->dump());

        $this->assertAttributeEquals('get', 'method', $obj);
        $this->assertAttributeEquals(false, 'https', $obj);
        $this->assertAttributeEquals('www.example.com', 'url', $obj);
        $this->assertAttributeEquals('/name/of/page', 'urn', $obj);

        $this->assertAttributeInstanceOf('Maverick\DataStructure\ReadOnlyMap', 'queryData', $obj);
        $this->assertEquals($get, $obj->getQueryData()->dump());
        $this->assertAttributeInstanceOf('Maverick\DataStructure\ReadOnlyMap', 'data', $obj);
        $this->assertEquals($post, $obj->getData()->dump());
    }

    public function testCustomRequestMethod() {
        $_POST['__METHOD__'] = 'CUSTOM';

        $obj = new Request([
            'REQUEST_METHOD' => 'POST'
        ]);

        $this->assertAttributeEquals('custom', 'method', $obj);

        $_POST = [];
    }

    public function testHttpsOn() {
        $obj = new Request([
            'HTTPS' => 'on'
        ]);

        $this->assertAttributeEquals(true, 'https', $obj);
    }

    public function testGetUri() {
        $obj = new Request([
            'HTTPS'       => 'on',
            'REQUEST_URI' => '/test/page',
            'SERVER_NAME' => 'www.domain.com/'
        ]);

        $this->assertEquals('https://www.domain.com/test/page', $obj->getUri());
    }
}