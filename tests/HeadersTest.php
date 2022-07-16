<?php

namespace Swiftly\Http\Tests;

use Swiftly\Http\Headers;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
Class HeadersTest Extends TestCase
{

    /** @var Headers $headers */
    private $headers;

    protected function setUp() : void
    {
        $this->headers = new Headers([
            'Accept' => '*/*',
            'Content-Type' => 'text/html',
            'User-Agent' => 'Swiftly/1'
        ]);
    }

    public function testCanGetSingleHeaderValue() : void
    {
        self::assertSame( '*/*', $this->headers->get( 'Accept' ) );
        self::assertSame( 'text/html', $this->headers->get( 'Content-Type' ) );
        self::assertNull( $this->headers->get( 'Unknown' ) );
    }

    public function testCanGetAllHeaderValues() : void
    {
        self::assertSame( ['*/*'], $this->headers->all( 'Accept' ) );
        self::assertSame( ['Swiftly/1'], $this->headers->all( 'User-Agent' ) );
    }

    public function testCanGetAllHeaders() : void
    {
        self::assertSame([
            'accept' => ['*/*'],
            'content-type' => ['text/html'],
            'user-agent' => ['Swiftly/1']
        ], $this->headers->all() );
    }

    public function testCanSetHeaderValue() : void
    {
        $this->headers->set( 'Cache-Control', 'no-store' );

        self::assertSame( 'no-store', $this->headers->get( 'Cache-Control' ) );
    }

    public function testCanReplaceHeaderValue() : void
    {
        $this->headers->set( 'Accept', 'text/html' );

        self::assertSame( 'text/html', $this->headers->get( 'Accept' ) );
        self::assertSame( ['text/html'], $this->headers->all( 'Accept' ) );
    }

    public function testCanAppendHeaderValue() : void
    {
        $this->headers->set( 'Accept', 'text/html', false );

        self::assertSame( '*/*', $this->headers->get( 'Accept' ) );
        self::assertContains( 'text/html', $this->headers->all( 'Accept' ) );
    }

    public function testCanCheckHeaderExists() : void
    {
        self::assertTrue( $this->headers->has( 'Accept' ) );
        self::assertFalse( $this->headers->has( 'Content-Length' ) );
    }
}
