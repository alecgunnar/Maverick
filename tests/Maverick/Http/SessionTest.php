<?php

use Maverick\Http\Session,
    Maverick\Http\Request,
    Maverick\Http\Response,
    Maverick\Http\Session\Cookie;

class SessionTest extends PHPUnit_Framework_Testcase {
    public function testConstruct() {
        $obj = new Session();

        $this->assertAttributeInstanceOf('Maverick\DataStructure\ReadOnlyMap', 'cookies', $obj);
        $this->assertAttributeInstanceOf('Maverick\DataStructure\Map', 'newCookies', $obj);
    }

    public function testDeleteCookie() {
        $_COOKIE['abc'] = '123';
        $obj = new Session();

        $cookie = $obj->getCookies()->get('abc');
        $obj->deleteCookie($cookie);

        $now = new \DateTime('now');

        $this->assertTrue($cookie->getExpiration() < $now);
    }

    public function testAddCookie() {
        $obj    = new Session();
        $cookie = new Cookie('abc', '123');

        $obj->addCookie($cookie);
        $this->assertTrue($obj->getNewCookies()->has('abc'));
    }

    public function testGetRedirectMessage() {
        $msg              = 'test';
        $_COOKIE['flash'] = $msg;
        $obj              = new Session();

        $this->assertEquals($msg, $obj->getRedirectMessage());
    }
}