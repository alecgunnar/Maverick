<?php

namespace Maverick\Router\Entity;

use PHPUnit_Framework_TestCase;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Response;
use Maverick\Testing\Utility\GenericCallable;

/**
 * @coversDefaultClass Maverick\Router\Entity\RouteEntity
 */
class RouteEntityTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructorSetsAttributes()
    {
        $methods = ['GET', 'POST'];
        $path = '/journey/to/mars';

        $instance = new RouteEntity($methods, $path);

        $this->assertAttributeEquals($methods, 'methods', $instance);
        $this->assertAttributeEquals($path, 'path', $instance);
    }

    /**
     * @covers ::__construct
     * @covers ::cleanPath
     */
    public function testConstructorCleansPath()
    {
        $given = '/hello/world/';
        $expected = '/hello/world';

        $instance = new RouteEntity([], $given, function() { });

        $this->assertAttributeEquals($expected, 'path', $instance);
    }

    /**
     * @covers ::__construct
     * @covers ::cleanPath
     */
    public function testConstructorPermitsLoneSlash()
    {
        $given = $expected = '/';

        $instance = new RouteEntity([], $given, function() { });

        $this->assertAttributeEquals($expected, 'path', $instance);
    }

    /**
     * @covers ::withMethods
     */
    public function testWithMethodsSetsMethods()
    {
        $given = $expected = ['GET', 'POST'];

        $instance = new RouteEntity();

        $instance->withMethods($given);

        $this->assertAttributeEquals($expected, 'methods', $instance);
    }

    /**
     * @covers ::withMethods
     */
    public function testWithMethodsReturnsSelf()
    {
        $instance = new RouteEntity();

        $ret = $instance->withMethods([]);

        $this->assertSame($instance, $ret);
    }

    /**
     * @covers ::getMethods
     * @depends testConstructorSetsAttributes
     */
    public function testGetMethodsReturnsMethods()
    {
        $given = $expected = ['GET', 'POST'];

        $instance = new RouteEntity($given);

        $this->assertEquals($expected, $instance->getMethods());
    }

    /**
     * @covers ::setPath
     */
    public function testSetPathSetsPath()
    {
        $given = $expected = '/journey/to/mars';

        $instance = new RouteEntity();

        $instance->setPath($given);

        $this->assertAttributeEquals($expected, 'path', $instance);
    }

    /**
     * @covers ::setPath
     * @covers ::cleanPath
     */
    public function testSetPathCleansPath()
    {
        $given = '/hello/world/';
        $expected = '/hello/world';

        $instance = new RouteEntity();

        $instance->setPath($given);

        $this->assertAttributeEquals($expected, 'path', $instance);
    }

    /**
     * @covers ::setPath
     * @covers ::cleanPath
     */
    public function testSetPathPermitsLoneSlash()
    {
        $given = $expected = '/';

        $instance = new RouteEntity();

        $instance->setPath($given);

        $this->assertAttributeEquals($expected, 'path', $instance);
    }

    /**
     * @covers ::setPath
     */
    public function testSetPathReturnsSelf()
    {
        $instance = new RouteEntity();

        $ret = $instance->setPath('/');

        $this->assertSame($instance, $ret);
    }

    /**
     * @covers ::getPath
     * @depends testConstructorSetsAttributes
     */
    public function testGetPathReturnsPath()
    {
        $given = $expected = '/journey/to/mars';

        $instance = new RouteEntity([], $given);

        $this->assertEquals($expected, $instance->getPath());
    }

    /**
     * @covers ::withPrefix
     * @depends testConstructorSetsAttributes
     */
    public function testWithPrefixPrependsStringToPath()
    {
        $path = '/world';
        $prefix = '/hello';
        $expected = $prefix . $path;

        $instance = new RouteEntity([], $path);

        $instance->withPrefix($prefix);

        $this->assertAttributeEquals($expected, 'path', $instance);
    }

    /**
     * @covers ::withPrefix
     * @covers ::cleanPath
     * @depends testConstructorSetsAttributes
     */
    public function testWithPrefixDoesNotPrependLoneSlash()
    {
        $path = '/world';
        $prefix = '/';
        $expected = $path;

        $instance = new RouteEntity([], $path);

        $instance->withPrefix($prefix);

        $this->assertAttributeEquals($expected, 'path', $instance);
    }

    /**
     * @covers ::withPrefix
     * @covers ::cleanPath
     * @depends testConstructorSetsAttributes
     */
    public function testWithPrefixDoesNotAddEndingSlash()
    {
        $path = '/world';
        $prefix = '/hello';
        $expected = $prefix . $path;
        $prefix .= '/';

        $instance = new RouteEntity([], $path);

        $instance->withPrefix($prefix);

        $this->assertAttributeEquals($expected, 'path', $instance);
    }

    /**
     * @covers ::withPrefix
     * @covers ::cleanPath
     */
    public function testWithPrefixReturnsSelf()
    {
        $instance = new RouteEntity([], '/', function() { });

        $ret = $instance->withPrefix('/');

        $this->assertSame($instance, $ret);
    }
}
