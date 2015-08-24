<?php
/**
 * Maverick
 *
 * @author Alec Carpenter <gunnar94@me.com>
 */

use Maverick\Router\Router;

/**
 * @covers Maverick\Router\Router
 */
class RouterTest extends PHPUnit_Framework_TestCase
{
    const CLASS_NAME  = '\\Maverick\\Router\\Router';

    protected function getInstance($fileLocator=null)
    {
        return new Router();
    }

    public function testForClassAttributes()
    {
        $this->getInstance();
        $this->assertClassHasAttribute('collection', self::CLASS_NAME);
    }
}