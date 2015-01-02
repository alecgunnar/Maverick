<?php

use Maverick\Http\Response,
    Maverick\Http\ResponseInstruction\RedirectResponseInstruction;

class RedirectResponseInstructionTest extends PHPUnit_Framework_Testcase {
    public function testConstruct() {
        $obj = new RedirectResponseInstruction('/dev', 'message', 305);

        $this->assertAttributeEquals('/dev', 'uri', $obj);
        $this->assertAttributeEquals('message', 'message', $obj);
        $this->assertAttributeEquals(305, 'code', $obj);
    }

    public function testFactory() {
        $obj = RedirectResponseInstruction::factory('/dev');

        $this->assertAttributeEquals('/dev', 'uri', $obj);
        $this->assertAttributeEquals('', 'message', $obj);
        $this->assertAttributeEquals(303, 'code', $obj);
    }

    public function testConstructAcceptsStringNumericCode() {
        $obj = RedirectResponseInstruction::factory('/dev', null, '305');

        $this->assertAttributeEquals(305, 'code', $obj);
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testConstructThrowsExceptionWithInvalidUri() {
        new RedirectResponseInstruction(false, '', 303);
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testConstructThrowsExceptionWithInvalidMessage() {
        new RedirectResponseInstruction('/dev', 123, 303);
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testConstructThrowsExceptionWithInvalidCodeType() {
        new RedirectResponseInstruction('/dev', null, false);
    }

    /**
     * @expectedException Maverick\Exception\InvalidValueException
     */
    public function testConstructThrowsExceptionWithInvalidCodeValue() {
        new RedirectResponseInstruction('/dev', null, 306);
    }
}
