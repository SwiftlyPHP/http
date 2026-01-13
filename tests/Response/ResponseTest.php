<?php declare(strict_types=1);

namespace Swiftly\Http\Tests\Response;

use PHPUnit\Framework\TestCase;
use Swiftly\Http\CookieCollection;
use Swiftly\Http\HeaderCollection;
use Swiftly\Http\Response\Response;
use Swiftly\Http\Status;

/**
 * @covers \Swiftly\Http\Response\Response
 * @uses \Swiftly\Http\HeaderCollection
 * @uses \Swiftly\Http\CookieCollection
 */
final class ResponseTest extends TestCase
{
    private Response $response;

    public function setUp(): void
    {
        $this->response = new Response(
            Status::OK,
            'Hello world!',
            [
                'Content-Type' => 'text/plain'
            ]
        );
    }

    /** These properties are part of the API - they must be public */
    public function testRequiredPropertiesArePublic(): void
    {
        self::assertInstanceOf(HeaderCollection::class, $this->response->headers);
        self::assertInstanceOf(CookieCollection::class, $this->response->cookies);
    }

    public function testCanGetHttpStatusCode(): void
    {
        self::assertSame(Status::OK, $this->response->getStatus());
    }

    public function testCanGetContent(): void
    {
        self::assertSame('Hello world!', $this->response->getContent());
    }

    public function testCanGetContentType(): void
    {
        self::assertSame('text/plain', $this->response->getContentType());
    }

    public function testCanSetHttpStatusCode(): void
    {
        self::assertSame(Status::OK, $this->response->getStatus());

        $this->response->setStatus(Status::CONTINUE);

        self::assertSame(Status::CONTINUE, $this->response->getStatus());
    }

    public function testCanSetContent(): void
    {
        self::assertSame('Hello world!', $this->response->getContent());

        $this->response->setContent('Goodnight world!');

        self::assertSame('Goodnight world!', $this->response->getContent());
    }

    public function testCanSetContentType(): void
    {
        self::assertSame('text/plain', $this->response->getContentType());

        $this->response->setContentType('text/html');

        self::assertSame('text/html', $this->response->getContentType());
    }
}
