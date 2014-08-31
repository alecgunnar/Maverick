<?php

use Maverick\Http\Request,
    Maverick\Http\Session,
    Maverick\Http\Response,
    Maverick\Http\Response\Instruction\ErrorInstruction;

class ErrorInstructionTest extends PHPUnit_Framework_Testcase {
    public function testConstruct() {
        $obj = new ErrorInstruction(500);

        $this->assertAttributeEquals(500, 'code', $obj);
    }

    /**
     * @expectedException Maverick\Exception\InvalidValueException
     */
    public function testConstructorWithInvalidCodeThrowsException() {
        $obj = ErrorInstruction::factory(300);
    }

    public function testInstruct() {
        $code = 500;
        $res  = new Response(new Request(), new Session());
        $obj  = ErrorInstruction::factory($code);

        $obj->instruct($res);

        $this->assertEquals($code, $res->getStatus());
    }
}