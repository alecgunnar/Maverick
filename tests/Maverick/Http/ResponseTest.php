<?php

use Maverick\Application,
    Maverick\Http\Request,
    Maverick\Http\Response,
    Maverick\Http\Session;

class ResponseTest extends PHPUnit_Framework_Testcase {
    private function getObj() {
        $req     = new Request();
        $session = new Session();
        return new Response($req, $session);
    }

    public function testConstructor() {
        $obj = $this->getObj();

        $this->assertAttributeEquals(200, 'status', $obj);
        $this->assertAttributeInstanceOf('Maverick\DataStructure\ArrayList', 'headers', $obj);

        $this->assertEquals($obj->getHeaders()->dump(), ['Content-type: text/html']);
    }

    public function testSetHeaderWithString() {
        $obj = $this->getObj();
        $obj->setHeader('abc', '123');

        $this->assertTrue(in_array('abc: 123', $obj->getHeaders()->dump()));
    }

    public function testSetHeaderWithArray() {
        $obj = $this->getObj();
        $obj->setHeader(['abc' => '123']);

        $this->assertTrue(in_array('abc: 123', $obj->getHeaders()->dump()));
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testSetHeaderWithInvalidName() {
        $obj = $this->getObj();
        $obj->setHeader(false, '123');
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testSetHeaderWithInvalidValue() {
        $obj = $this->getObj();
        $obj->setHeader('123', false);
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testSetBodyWithInvalidContent() {
        $obj = $this->getObj();
        $obj->setBody(false);
    }

    public function testSetStatusCode() {
        $obj = $this->getObj();
        $obj->setStatus(404);
        $this->assertEquals(404, $obj->getStatus());
    }

    /**
     * @expectedException Maverick\Exception\InvalidValueException
     */
    public function testSetStatusWithInvalidCode() {
        $obj = $this->getObj();
        $obj->setStatus(306);
    }
}