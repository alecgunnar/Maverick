<?php

use Maverick\DataStructure\ReadOnlyMap;

class ReadOnlyMapTest extends PHPUnit_Framework_Testcase {
    public function testConstructor() {
        $obj = new ReadOnlyMap([
            'abc' => 'def',
            'ghi' => null
        ]);

        $this->assertAttributeEquals(['abc' => 'def'], 'data', $obj);
    }

    /** 
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testConstructorWithNonArrayArgument() {
        new ReadOnlyMap(false);
    }

    /**
     * @expectedException Maverick\Exception\UnavailableMethodException
     */
    public function testSetData() {
        $obj = new ReadOnlyMap;
        $obj->set(['a' => 'b', 'c' => 'd']);
    }
}