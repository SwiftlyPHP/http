<?php

namespace Swiftly\Http\Tests\Server;

use Swiftly\Http\Server\Response;
use Swiftly\Http\Status;
use PHPUnit\Framework\TestCase;

use function xdebug_get_headers;
use function http_response_code;

/**
 * @group Shared
 */
Class ResponseTest Extends TestCase
{

    /** @var Response $response */
    private $response;

    protected function setUp() : void
    {
        $this->response = new Response( '<html></html>', Status::OK, [
            'Content-Type' => 'text/html',
            'Cache-Control' => 'no-cache',
            'Content-Length' => '13'
        ]);
    }

    public function testCanGetStatusCode() : void
    {
        self::assertSame( Status::OK, $this->response->getStatus() );
    }

    public function testCanSetStatusCode() : void
    {
        $this->response->setStatus( Status::NOT_FOUND );

        self::assertSame( Status::NOT_FOUND, $this->response->getStatus() );
    }

    public function testCanGetContent() : void
    {
        self::assertSame( '<html></html>', $this->response->getContent() );
    }

    public function testCanSetContent() : void
    {
        $this->response->setContent( '{"life":42}' );

        self::assertSame( '{"life":42}', $this->response->getContent() );
    }

    public function testCanSetContentType() : void
    {
        $this->response->setContentType( 'text/xml' );

        self::assertSame( 'text/xml', $this->response->headers->get(
            'Content-Type'
        ));
    }

    /**
     * @runInSeparateProcess
     * @requires extension xdebug
     */
    public function testCanSendResponse() : void
    {
        self::expectOutputString( '<html></html>' );

        $this->response->send();

        // headers_list doesn't work when running on the CLI
        $headers = xdebug_get_headers();

        self::assertSame( Status::OK, http_response_code() );
        self::assertContains( 'cache-control: no-cache', $headers );
        self::assertContains( 'content-length: 13', $headers );
    }
}
