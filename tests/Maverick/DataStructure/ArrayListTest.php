<?php

use Maverick\DataStructure\ArrayList;

class ArrayListTest extends PHPUnit_Framework_Testcase {
    public function testConstruct() {
        $data = ['a', 'b', 'c'];
        $obj  = new ArrayList($data + [null]);

        $this->assertAttributeEquals($data, 'data', $obj);
    }

    public function testAddToList() {
        $obj   = new ArrayList();
        $items = ['a', 'b', 'c'];
        $text  = 'this is an item';

        $obj->add($text);
        $obj->add($items);

        $this->assertAttributeEquals([$text, 'a', 'b', 'c'], 'data', $obj);
    }

    public function testIterate() {
        $data = ['a', 'b', 'c', 'd'];
        $obj  = new ArrayList($data);
        $test = [];

        foreach($obj as $val) {
            $test[] = $val;
        }

        $this->assertEquals($data, $test);
        $this->assertEquals(count($data), $obj->getLength());
    }

    public function testGetKey() {
        $data = ['a', 'b', 'c', 'd'];
        $obj  = new ArrayList($data);

        foreach($obj as $key => $val) {
            if($key == 2)
                break;
        }

        $this->assertEquals(2, $obj->key());
    }
}