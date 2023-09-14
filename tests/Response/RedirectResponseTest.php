<?php

namespace Swiftly\Http\Tests\Response;

use PHPUnit\Framework\TestCase;
use Swiftly\Http\Response\RedirectResponse;
use Swiftly\Http\Status;

/**
 * @covers \Swiftly\Http\Response\RedirectResponse
 * @covers \Swiftly\Http\Response\Response
 * @uses \Swiftly\Http\HeaderCollection
 * @uses \Swiftly\Http\CookieCollection
 */
final class RedirectResponseTest extends TestCase
{
    private RedirectResponse $response;

    public function setUp(): void
    {
        $this->response = new RedirectResponse('http://example.com/new');
    }

    public function testIsCorrectHttpStatusCode(): void
    {
        self::assertSame(Status::SEE_OTHER, $this->response->getStatus());
    }

    public function testHasLocationHeaderSet(): void
    {
        self::assertSame(
            'http://example.com/new',
            $this->response->headers->get('Location')
        );
    }

    public function testHasLocationUrlInContent(): void
    {
        // Not a hard requirement, but good practice
        self::assertStringContainsString(
            'http://example.com/new',
            $this->response->getContent()
        );
    }
}
