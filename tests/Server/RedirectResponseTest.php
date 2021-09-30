<?php

namespace Swiftly\Http\Tests\Server;

use Swiftly\Http\Server\RedirectResponse;
use Swiftly\Http\Status;
use PHPUnit\Framework\TestCase;

use function xdebug_get_headers;
use function http_response_code;

/**
 * @group Shared
 */
Class RedirectResponseTest Extends TestCase
{

    /** @var RedirectResponse $response */
    private $response;

    protected function setUp() : void
    {
        $this->response = new RedirectResponse( 'https://example.com',
            Status::PERMANENT_REDIRECT,
            [
                'Content-Type' => 'text/html',
                'Cache-Control' => 'no-cache'
            ]
        );
    }

    /**
     * @runInSeparateProcess
     * @requires extension xdebug
     */
    public function testCanSendRedirectResponse() : void
    {
        self::expectOutputString( 'Redirecting to: https://example.com' );

        $this->response->send();

        // headers_list doesn't work when running on the CLI
        $headers = xdebug_get_headers();

        self::assertSame( Status::PERMANENT_REDIRECT, http_response_code() );
        self::assertContains( 'cache-control: no-cache', $headers );
    }
}
