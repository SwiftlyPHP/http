<?php

namespace Swiftly\Http\Tests;

use Swiftly\Http\Cookie;
use Swiftly\Http\Cookies;
use PHPUnit\Framework\TestCase;

/**
 * @group Shared
 */
Class CookiesTest Extends TestCase
{

    /** @var Cookies $cookies */
    private $cookies;

    protected function setUp() : void
    {
        $test_cookie = new Cookie();
        $test_cookie->name = 'test_id';
        $test_cookie->value = '42';

        $this->cookies = new Cookies([ $test_cookie ]);
    }

    private function exampleCookie() : Cookie
    {
        $cookie = new Cookie();
        $cookie->name = 'example';
        $cookie->value = 'value';

        return $cookie;
    }

    public function testCanGetCookie() : void
    {
        $cookie = $this->cookies->get( 'test_id' );

        self::assertInstanceOf( Cookie::class, $cookie );
        self::assertSame( 'test_id', $cookie->name );
        self::assertSame( '42', $cookie->value );
    }

    public function testCanSetCookie() : void
    {
        $this->cookies->set(
            $this->exampleCookie()
        );

        $cookie = $this->cookies->get( 'example' );

        self::assertInstanceOf( Cookie::class, $cookie );
        self::assertSame( 'example', $cookie->name );
        self::assertSame( 'value', $cookie->value );
    }

    public function testCanAddNewCookie() : void
    {
        $this->cookies->add( 'example', 'value' );

        $cookie = $this->cookies->get( 'example' );

        self::assertInstanceOf( Cookie::class, $cookie );
        self::assertSame( 'example', $cookie->name );
        self::assertSame( 'value', $cookie->value );
    }

    public function testCanCheckCookieExists() : void
    {
        self::assertTrue( $this->cookies->has( 'test_id' ) );
        self::assertFalse( $this->cookies->has( 'unknown' ) );
    }

    public function testCanGetAllCookies() : void
    {
        $cookies = $this->cookies->all();

        self::assertArrayHasKey( 'test_id', $cookies );
        self::assertInstanceOf( Cookie::class, $cookies['test_id'] );
        self::assertSame( 'test_id', $cookies['test_id']->name );
        self::assertSame( '42', $cookies['test_id']->value );
    }
}
