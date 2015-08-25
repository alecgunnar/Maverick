<?php
/**
 * Maverick
 *
 * @package Maverick
 * @author  Alec Carpenter <gunnar94@me.com>
 */

use Maverick\Collection\ControllerCollection;
use Maverick\Http\StandardResponse;

/**
 * @covers Maverick\Container\ControllerCollection
 */
class ControllerCollectionTest extends PHPUnit_Framework_TestCase
{
    const CLASS_NAME = 'Maverick\\Collection\\ControllerCollection';

    private function getInstance()
    {
        return new ControllerCollection();
    }

    public function testClassHasAttributes()
    {
        $this->getInstance();
        $this->assertClassHasAttribute('controllers', self::CLASS_NAME);
    }

    /**
     * @covers Maverick\Container\ControllerCollection::add
     */
    public function testAddControllerToList()
    {
        $instance = $this->getInstance();

        $name       = 'test.controller';
        $controller = new TestController(StandardResponse::create());

        $instance->add($name, $controller);

        $this->assertAttributeEquals([$name => $controller], 'controllers', $instance);
    }

    /**
     * @covers Maverick\Container\ControllerCollection::get
     */
    public function testGetControllerByNameReturnsControllerIfItExists()
    {
        $instance = $this->getInstance();

        $name       = 'test.controller';
        $controller = new TestController(StandardResponse::create());

        $instance->add($name, $controller);

        $this->assertEquals($instance->get($name), $controller);
    }

    /**
     * @covers Maverick\Container\ControllerCollection::get
     */
    public function testGetControllerByNameReturnsFalseIfItDoesNotExist()
    {
        $this->assertEquals($this->getInstance()->get('test.controller'), false);
    }
}