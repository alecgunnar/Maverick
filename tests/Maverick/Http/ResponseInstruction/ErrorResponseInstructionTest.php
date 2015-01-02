<?php

use Maverick\Http\Request,
    Maverick\Http\Session,
    Maverick\Http\Response,
    Maverick\Http\ResponseInstruction\ErrorResponseInstruction;

class ErrorResponseInstructionTest extends PHPUnit_Framework_Testcase {
    public function testConstruct() {
        $obj = new ErrorResponseInstruction(500);

        $this->assertAttributeEquals(500, 'code', $obj);
    }

    /**
     * @expectedException Maverick\Exception\InvalidValueException
     */
    public function testConstructorWithInvalidCodeThrowsException() {
        $obj = ErrorResponseInstruction::factory(300);
    }

    public function testInstruct() {
        $code = 500;
        $res  = new Response(new Request(), new Session());
        $obj  = ErrorResponseInstruction::factory($code);

        $obj->instruct($res);

        $this->assertEquals($code, $res->getStatus());
    }
}
