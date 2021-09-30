<?php

namespace Swiftly\Http\Tests\Server;

use Swiftly\Http\Server\JsonResponse;
use Swiftly\Http\Status;
use PHPUnit\Framework\TestCase;

use function json_encode;
use function xdebug_get_headers;
use function http_response_code;

use const JSON_PRETTY_PRINT;
use const JSON_HEX_AMP;
use const JSON_HEX_TAG;
use const JSON_HEX_APOS;
use const JSON_HEX_QUOT;

/**
 * @group Shared
 */
Class JsonResponseTest Extends TestCase
{

    private const EXAMPLE_JSON = [
        'text' => 'some_text',
        'life' => 42,
        'deep' => [
            'float' => 1.5,
            'array' => [1, 2, 3]
        ]
    ];

    /** @var JsonResponse $response */
    private $response;

    protected function setUp() : void
    {
        $this->response = new JsonResponse( self::EXAMPLE_JSON, Status::OK, [
            'Cache-Control' => 'no-cache'
        ]);
    }

    /**
     * @depends testCanSendJsonResponse
     * @runInSeparateProcess
     * @requires extension xdebug
     */
    public function testCanSetJsonContent() : void
    {
        $this->response->setJson(['test' => 123]);

        self::expectOutputString( '{"test":123}' );

        $this->response->send();
    }

    /**
     * @depends testCanSendJsonResponse
     * @runInSeparateProcess
     * @requires extension xdebug
     */
    public function testCanSetJsonEncoding() : void
    {
        $this->response->setEncoding( JSON_PRETTY_PRINT );

        self::expectOutputString(
            json_encode( self::EXAMPLE_JSON, JSON_PRETTY_PRINT )
        );

        $this->response->send();
    }

    /**
     * @runInSeparateProcess
     * @requires extension xdebug
     */
    public function testCanSendJsonResponse() : void
    {
        $json = json_encode(
            self::EXAMPLE_JSON,
            JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT
        );

        self::expectOutputString( $json );

        $this->response->send();

        // headers_list doesn't work when running on the CLI
        $headers = xdebug_get_headers();

        self::assertSame( Status::OK, http_response_code() );
        self::assertContains( 'cache-control: no-cache', $headers );
    }
}
