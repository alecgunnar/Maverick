<?php

use Maverick\Http\Request,
    Maverick\Http\Response,
    Maverick\Http\Session\Cookie;

class ResponseTest extends PHPUnit_Framework_Testcase {
    public function testConstructor() {
        $req = new Request();
        $obj = new Response($req);

        $this->assertAttributeEquals(200, 'status', $obj);
        $this->assertAttributeInstanceOf('Maverick\DataStructure\ArrayList', 'headers', $obj);
    }

    /**
     * @expectedException Maverick\Exception\InvalidArgumentException
     */
    public function testSetHeaderWithInvalidName() {
        $req = new Request();
        $obj = new Response($req);
        $obj->setHeader(false, '123');
    }

    /**
     * @expectedException Maverick\Exception\InvalidArgumentException
     */
    public function testSetHeaderWithInvalidValue() {
        $req = new Request();
        $obj = new Response($req);
        $obj->setHeader('123', false);
    }

    /**
     * @expectedException Maverick\Exception\InvalidArgumentException
     */
    public function testSetBodyWithInvalidContent() {
        $req = new Request();
        $obj = new Response($req);
        $obj->setBody(false);
    }

    public function testSetStatusCode() {
        $req = new Request();
        $obj = new Response($req);

        $obj->setStatus(404);

        $this->assertEquals(404, $obj->getStatus());
    }

    /**
     * @expectedException Maverick\Exception\InvalidValueException
     */
    public function testSetStatusWithInvalidCode() {
        $req = new Request;
        $obj = new Response($req);

        $obj->setStatus(306);
    }
}