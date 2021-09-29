<?php

namespace Swiftly\Http\Tests;

use Swiftly\Http\Url;
use PHPUnit\Framework\TestCase;

/**
 * @group Shared
 * @backupGlobals enabled
 */
Class UrlTest Extends TestCase
{

    public static function setUpBeforeClass() : void
    {
        $_SERVER['HTTP_HOST'] = 'localhost'; $_SERVER['REQUEST_URI'] = '/';
    }

    public function testCanCreateUrlFromValidString() : void
    {
        $url = Url::fromString( 'http://test.co.uk/example?page=1#id' );

        self::assertSame( 'http',       $url->scheme );
        self::assertSame( 'test.co.uk', $url->domain );
        self::assertSame( '/example',   $url->path );
        self::assertSame( 'page=1',     $url->query );
        self::assertSame( 'id',         $url->fragment );
    }

    public function testCanCreateUrlFromMalformedString() : void
    {
        $url = Url::fromString( 'i:///@bad' );

        self::assertSame( 'http', $url->scheme );
        self::assertSame( '', $url->domain );
        self::assertSame( '', $url->path );
        self::assertSame( '', $url->query );
        self::assertSame( '', $url->fragment );
    }

    public function testCanCreateUrlFromGlobals() : void
    {
        $url = Url::fromGlobals();

        self::assertSame( 'http', $url->scheme );
        self::assertSame( 'localhost', $url->domain );
        self::assertSame( '/', $url->path );
        self::assertSame( '', $url->query );
        self::assertSame( '', $url->fragment );
    }

    public function testCanCreateUrlFromGlobalsBehindProxy() : void
    {
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';

        $url = Url::fromGlobals();

        self::assertSame( 'https', $url->scheme );
        self::assertSame( 'localhost', $url->domain );
        self::assertSame( '/', $url->path );
        self::assertSame( '', $url->query );
        self::assertSame( '', $url->fragment );
    }

    public function testCanCreateUrlFromGlobalsOnIss() : void
    {
        $_SERVER['HTTPS'] = 'on';

        $url = Url::fromGlobals();

        self::assertSame( 'https', $url->scheme );
        self::assertSame( 'localhost', $url->domain );
        self::assertSame( '/', $url->path );
        self::assertSame( '', $url->query );
        self::assertSame( '', $url->fragment );
    }

    public function testCanCastUrlIntoString() : void
    {
        $url = new Url;
        $url->scheme = 'http';
        $url->domain = 'test.co.uk';
        $url->path = '/example';
        $url->query = 'page=1';
        $url->fragment = 'id';

        self::assertSame( 'http://test.co.uk/example?page=1#id', (string)$url );
    }

    public function testValidUrlReturnsTrue() : void
    {
        $url = Url::fromString( 'http://test.co.uk/example?page=1#id' );

        self::assertTrue( $url->valid() );
    }

    public function testInvalidUrlReturnsFalse() : void
    {
        $url = Url::fromString( 'bad:/broken.co@url' );

        self::assertFalse( $url->valid() );
    }
}
