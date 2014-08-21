<?php

use Maverick\DataStructure\Map;

class MapTest extends PHPUnit_Framework_Testcase {
    public function testConstructorWithNoArgs() {
        $obj = new Map();
        $this->assertAttributeEquals([], 'data', $obj);
    }

    public function testConstructorWithArgs() {
        $data = ['a' => 'b', 'c' => 'd'];
        $obj  = new Map($data);

        $this->assertAttributeEquals($data, 'data', $obj);
    }

    public function testSetData() {
        $obj  = new Map;
        $data = ['a' => 'b', 'c' => 'd'];

        $obj->set($data);
        $obj->set('e', 'f');
        $obj->set('g', null);

        $this->assertAttributeEquals($data + ['e' => 'f'], 'data', $obj); 
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testSetDataWithInvalidArg() {
        $obj = new Map;
        $obj->set(false);
    }

    public function testUnsetNulledKey() {
        $obj = new Map(['a' => 'b', 'c' => 'd']);

        $obj->set('a', null);

        $this->assertAttributeEquals(['c' => 'd'], 'data', $obj);
    }

    public function testDataExists() {
        $obj = new Map(['a' => 'b', 'c' => null]);

        $this->assertTrue($obj->has('a'));
        $this->assertFalse($obj->has('c'));
        $this->assertFalse($obj->has('e'));
    }

    public function testGetData() {
        $var = 'abc';
        $val = 'def';
        $obj = new Map([$var => $val]);

        $this->assertEquals($val, $obj->get($var));
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testGetDataWithNonStringThrowsException() {
        $obj = new Map();
        $obj->get(false);
    }

    public function testGetUnsetData() {
        $obj = new Map();

        $this->assertEquals(null, $obj->get('unset.value'));
    }

    public function testIterate() {
        $data = ['a' => 'b', 'c' => 'd', 'e' => 'f', 'g' => 'h'];
        $obj  = new Map($data);
        $test = [];

        foreach($obj as $key => $val) {
            $test[$key] = $val;
        }

        $this->assertEquals($data, $test);
        $this->assertEquals(count($data), $obj->getLength());
    }

    public function testGetKey() {
        $data = ['a' => 'b', 'c' => 'd', 'e' => 'f', 'g' => 'h'];
        $obj  = new Map($data);

        foreach($obj as $key => $val) {
            if($key == 'c')
                break;
        }

        $this->assertEquals('c', $obj->key());
    }
}