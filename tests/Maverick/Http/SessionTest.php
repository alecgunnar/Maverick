<?php

use Maverick\Http\Session,
    Maverick\Http\Request,
    Maverick\Http\Response;

class SessionTest extends PHPUnit_Framework_Testcase {
    public function testConstruct() {
        $obj = new Session();

        $this->assertAttributeInstanceOf('Maverick\DataStructure\UserInputMap', 'cookies', $obj);
    }
}