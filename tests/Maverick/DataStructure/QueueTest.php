<?php

use Maverick\DataStructure\Queue;

class QueueTest extends PHPUnit_Framework_Testcase {
    public function testConstructor() {
        $data = ['abc', '123'];
        $obj  = new Queue($data);

        $this->assertAttributeEquals($data, 'data', $obj);
    }

    public function testEnqueue() {
        $data = ['abc', '123'];
        $obj  = new Queue();

        $obj->enqueue($data);

        $this->assertAttributeEquals($data, 'data', $obj);
    }

    public function testDequeue() {
        $obj = new Queue(['abc', '123']);

        $this->assertEquals('abc', $obj->dequeue());
        $this->assertAttributeEquals(['123'], 'data', $obj);
    }

    public function testReturnNullWhenEmpty() {
        $obj = new Queue();

        $this->assertNull($obj->dequeue());
    }
}