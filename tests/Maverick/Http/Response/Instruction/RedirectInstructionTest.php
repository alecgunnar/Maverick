<?php

use Maverick\Http\Response,
    Maverick\Http\Response\Instruction\RedirectInstruction;

class RedirectInstructionTest extends PHPUnit_Framework_Testcase {
    public function testConstruct() {
        $obj = new RedirectInstruction('/dev');

        $this->assertAttributeEquals('/dev', 'uri', $obj);
        $this->assertAttributeEquals(307, 'code', $obj);
    }

    public function testConstructAcceptsStringNumericCode() {
        $obj = new RedirectInstruction('/dev', '305');

        $this->assertAttributeEquals(305, 'code', $obj);
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testConstructThrowsExceptionWithInvalidUri() {
        new RedirectInstruction(false);
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testConstructThrowsExceptionWithInvalidCodeType() {
        new RedirectInstruction('/dev', false);
    }

    /**
     * @expectedException Maverick\Exception\InvalidValueException
     */
    public function testConstructThrowsExceptionWithInvalidCodeValue() {
        new RedirectInstruction('/dev', 306);
    }
}