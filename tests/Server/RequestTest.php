<?php

namespace Swiftly\Http\Tests\Server;

use Swiftly\Http\Server\Request;
use Swiftly\Http\Url;
use Swiftly\Http\Headers;
use Swiftly\Http\Cookies;
use Swiftly\Http\Cookie;
use Swiftly\Http\Parameters;
use PHPUnit\Framework\TestCase;

/**
 * @group Shared
 */
Class RequestTest Extends TestCase
{

    /** @var Request $request */
    private $request;

    /** @var Url $url */
    private $url;

    protected function setUp() : void
    {
        $this->url = Url::fromString( 'https://test.co.uk/example?page=1#id' );

        $this->request = new Request(
            'POST',
            $this->url,
            new Headers([
                'Content-Type' => 'text/html',
                'Cache-Control' => 'no-cache'
            ]),
            new Cookies([
                $this->exampleCookie()
            ]),
            new Parameters([
                'page' => '1'
            ]),
            new Parameters([
                'name' => 'John'
            ])
        );
    }

    private function exampleCookie() : Cookie
    {
        $cookie = new Cookie;
        $cookie->name = 'example';
        $cookie->value = 'value';

        return $cookie;
    }

    public function testHasPublicHeadersBag() : void
    {
        self::assertInstanceOf( Headers::class, $this->request->headers );
        self::assertSame( 'no-cache',
            $this->request->headers->get( 'Cache-Control' )
        );
    }

    public function testHasPublicCookiesBag() : void
    {
        self::assertInstanceOf( Cookies::class, $this->request->cookies );
        self::assertInstanceOf( Cookie::class,
            $this->request->cookies->get( 'example' )
        );
        self::assertSame( 'value',
            $this->request->cookies->get( 'example' )->value
        );
    }

    public function testHasPublicQueryParameters() : void
    {
        self::assertInstanceOf( Parameters::class, $this->request->query );
        self::assertSame( '1',
            $this->request->query->get( 'page' )
        );
    }

    public function testHasPublicPostParameters() : void
    {
        self::assertInstanceOf( Parameters::class, $this->request->post );
        self::assertSame( 'John',
            $this->request->post->get( 'name' )
        );
    }

    public function testCanGetRequestMethod() : void
    {
        self::assertSame( 'POST', $this->request->getMethod() );
    }

    public function testCanGetRequestProtocol() : void
    {
        self::assertSame( 'https', $this->request->getProtocol() );
    }

    public function testCanGetRequestPath() : void
    {
        self::assertSame( '/example', $this->request->getPath() );
    }

    public function testConsidersHttpsSecure() : void
    {
        self::assertTrue( $this->request->isSecure() );
    }

    public function testConsidersHttpInsecure() : void
    {
        $this->url->scheme = 'http';

        self::assertFalse( $this->request->isSecure() );
    }
}
