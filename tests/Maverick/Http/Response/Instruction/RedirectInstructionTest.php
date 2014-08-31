<?php

use Maverick\Http\Response,
    Maverick\Http\Response\Instruction\RedirectInstruction;

class RedirectInstructionTest extends PHPUnit_Framework_Testcase {
    public function testConstruct() {
        $obj = new RedirectInstruction('/dev', 'message', 305);

        $this->assertAttributeEquals('/dev', 'uri', $obj);
        $this->assertAttributeEquals('message', 'message', $obj);
        $this->assertAttributeEquals(305, 'code', $obj);
    }

    public function testFactory() {
        $obj = RedirectInstruction::factory('/dev');

        $this->assertAttributeEquals('/dev', 'uri', $obj);
        $this->assertAttributeEquals('', 'message', $obj);
        $this->assertAttributeEquals(303, 'code', $obj);
    }

    public function testConstructAcceptsStringNumericCode() {
        $obj = RedirectInstruction::factory('/dev', null, '305');

        $this->assertAttributeEquals(305, 'code', $obj);
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testConstructThrowsExceptionWithInvalidUri() {
        new RedirectInstruction(false, '', 303);
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testConstructThrowsExceptionWithInvalidMessage() {
        new RedirectInstruction('/dev', 123, 303);
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testConstructThrowsExceptionWithInvalidCodeType() {
        new RedirectInstruction('/dev', null, false);
    }

    /**
     * @expectedException Maverick\Exception\InvalidValueException
     */
    public function testConstructThrowsExceptionWithInvalidCodeValue() {
        new RedirectInstruction('/dev', null, 306);
    }
}