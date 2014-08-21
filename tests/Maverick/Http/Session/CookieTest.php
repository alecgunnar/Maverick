<?php

use Maverick\Http\Session\Cookie;

class CookieTest extends PHPUnit_Framework_Testcase {
    public function testConstruct() {
        $name     = 'abc';
        $value    = '123';
        $expire   = new DateTime();
        $path     = '/path';
        $domain   = '*.domain.com';
        $secure   = true;
        $httpOnly = true;

        $obj = new Cookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);

        $this->assertEquals($name, $obj->getName());
        $this->assertEquals($value, $obj->getValue());
        $this->assertEquals($expire, $obj->getExpiration());
        $this->assertEquals($path, $obj->getPath());
        $this->assertEquals($domain, $obj->getDomain());
        $this->assertEquals($secure, $obj->isSecure());
        $this->assertEquals($httpOnly, $obj->isHttpOnly());
    }

    public function testToString() {
        $dateTime = new \DateTime();

        $name     = 'abc';
        $value    = '123';
        $expire   = $dateTime;
        $path     = '/path';
        $domain   = '*.domain.com';
        $secure   = true;
        $httpOnly = true;

        $obj = new Cookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);

        $this->assertEquals($name . '=' . $value . '; Domain=' . $domain . '; Path=' . $path . '; Expires=' . $dateTime->format(\DateTime::RFC1123) . '; Secure; HttpOnly;', (string)$obj);
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testSetNameThrowsExceptionWhenNonStringSupplied() {
        $obj = new Cookie('abc');
        $obj->setName(false);
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testSetValueThrowsExceptionWhenNonStringSupplied() {
        $obj = new Cookie('abc');
        $obj->setValue(false);
    }

    public function testSetExpirationWithNumericString() {
        $obj     = new Cookie('abc');
        $seconds = '123';        

        $obj->setExpiration($seconds); // Expires in 123 seconds

        $expireTime = new \DateTime();
        $expireTime->modify($seconds . ' seconds');

        $this->assertEquals($obj->getExpiration(), $expireTime);
    }

    public function testSetExpirationWithBools() {
        $obj     = new Cookie('abc');
        $seconds = '123';        

        $obj->setExpiration(false);

        $this->assertAttributeEquals(null, 'expire', $obj);

        $obj->setExpiration(true);

        $this->assertTrue($obj->getExpiration() < new \DateTime());
    }

    public function testSetExpirationWithObjectOfDateTime() {
        $obj     = new Cookie('abc');
        $seconds = '123';        

        $expireTime = new \DateTime();

        $obj->setExpiration($expireTime);

        $this->assertEquals($expireTime, $obj->getExpiration());
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testSetExpirationThrowsExceptionWhenNonStringSupplied() {
        $obj = new Cookie('abc');
        $obj->setExpiration('abc');
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testSetPathThrowsExceptionWhenNonStringSupplied() {
        $obj = new Cookie('abc');
        $obj->setPath(false);
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testSetDomainThrowsExceptionWhenNonStringSupplied() {
        $obj = new Cookie('abc');
        $obj->setDomain(false);
    }

    public function testSetSecure() {
        $obj = new Cookie('abc', '123', '', '', '', true);
        $obj->setSecure();

        $this->assertAttributeEquals(false, 'secure', $obj);
    }

    public function testSetHttpOnly() {
        $obj = new Cookie('abc', '123', '', '', '', null, true);
        $obj->setHttpOnly();

        $this->assertAttributeEquals(false, 'httpOnly', $obj);
    }
}