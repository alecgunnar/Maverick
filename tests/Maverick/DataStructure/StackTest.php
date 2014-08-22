<?php

use Maverick\DataStructure\Stack;

class StackTest extends PHPUnit_Framework_Testcase {
    public function testConstructor() {
        $data = ['abc', '123'];
        $obj  = new Stack($data);

        $this->assertAttributeEquals($data, 'data', $obj);
    }

    public function testPush() {
        $data = ['abc', '123'];
        $obj  = new Stack();

        $obj->push($data);

        $this->assertAttributeEquals($data, 'data', $obj);
    }

    public function testPop() {
        $obj = new Stack(['abc', '123']);

        $this->assertEquals('123', $obj->pop());
        $this->assertAttributeEquals(['abc'], 'data', $obj);
    }

    public function testReturnNullWhenEmpty() {
        $obj = new Stack();

        $this->assertNull($obj->pop());
    }
}