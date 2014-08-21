<?php

use Maverick\Application,
    Maverick\Http\Request,
    Maverick\Http\Response,
    Maverick\Http\Session;

class ResponseTest extends PHPUnit_Framework_Testcase {
    public function testConstructor() {
        $req     = new Request();
        $session = new Session();
        $obj     = new Response($req, $session);

        $this->assertAttributeEquals(200, 'status', $obj);
        $this->assertAttributeInstanceOf('Maverick\DataStructure\ArrayList', 'headers', $obj);

        $this->assertEquals($obj->getHeaders()->dump(), ['Content-type: text/html']);
    }

    public function testSetHeaderWithString() {
        $req     = new Request();
        $session = new Session();
        $obj     = new Response($req, $session);
        $obj->setHeader('abc', '123');

        $this->assertTrue(in_array('abc: 123', $obj->getHeaders()->dump()));
    }

    public function testSetHeaderWithArray() {
        $req     = new Request();
        $session = new Session();
        $obj     = new Response($req, $session);
        $obj->setHeader(['abc' => '123']);

        $this->assertTrue(in_array('abc: 123', $obj->getHeaders()->dump()));
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testSetHeaderWithInvalidName() {
        $req     = new Request();
        $session = new Session();
        $obj     = new Response($req, $session);
        $obj->setHeader(false, '123');
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testSetHeaderWithInvalidValue() {
        $req     = new Request();
        $session = new Session();
        $obj     = new Response($req, $session);
        $obj->setHeader('123', false);
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testSetBodyWithInvalidContent() {
        $req     = new Request();
        $session = new Session();
        $obj     = new Response($req, $session);
        $obj->setBody(false);
    }

    public function testSetStatusCode() {
        $req     = new Request();
        $session = new Session();
        $obj     = new Response($req, $session);

        $obj->setStatus(404);

        $this->assertEquals(404, $obj->getStatus());
    }

    /**
     * @expectedException Maverick\Exception\InvalidValueException
     */
    public function testSetStatusWithInvalidCode() {
        $req     = new Request();
        $session = new Session();
        $obj     = new Response($req, $session);

        $obj->setStatus(306);
    }

    /**
     * @expectedException Maverick\Exception\UnavailableMethodException
     */
    public function testSendThrowsExceptionIfHeadersAlreadySent() {
        $req     = new Request();
        $session = new Session();
        $obj     = new Response($req, $session);

        $obj->send();
    }
}