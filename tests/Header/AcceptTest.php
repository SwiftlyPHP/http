<?php declare(strict_types=1);

namespace Swiftly\Http\Tests\Header;

use PHPUnit\Framework\TestCase;
use Swiftly\Http\Exception\HeaderException;
use Swiftly\Http\Header\Accept;
use Swiftly\Http\Response\Response;

/**
 * @covers \Swiftly\Http\Header\Accept
 * @uses \Swiftly\Http\Helpers
 */
final class AcceptTest extends TestCase
{
    private Accept $accept;

    protected function setUp(): void
    {
        $this->accept = new Accept([
            'text/plain' => ['q=1'],
            'text/html' => true,
            'audio/*' => true,
        ]);
    }

    public function testCanFetchHeaderName(): void
    {
        self::assertSame('Accept', $this->accept::name());
    }

    public function testCanDetermineIfMimeTypeAllowed(): void
    {
        self::assertTrue($this->accept->allows('text/plain'));
        self::assertTrue($this->accept->allows('text/html'));
        self::assertTrue($this->accept->allows('audio/wav'));

        self::assertFalse($this->accept->allows('text/csv'));
        self::assertFalse($this->accept->allows('application/*'));
        self::assertFalse($this->accept->allows('*/*'));
    }

    public function testEmptyHeaderAllowsAllMimeTypes(): void
    {
        $accept = new Accept([]);

        self::assertTrue($accept->allows('text/plain'));
        self::assertTrue($accept->allows('application/json'));
        self::assertTrue($accept->allows('audio/wav'));
        self::assertTrue($accept->allows('text/*'));
    }

    public function testWildcardHeaderAllowsAllMimeTypes(): void
    {
        $accept = new Accept(['*/*' => true]);

        self::assertTrue($accept->allows('text/plain'));
        self::assertTrue($accept->allows('application/json'));
        self::assertTrue($accept->allows('audio/wav'));
        self::assertTrue($accept->allows('text/*'));
    }

    public function testCanParseHeaderValue(): void
    {
        $accept = Accept::fromValue('text/html; q=1, text/plain, audio/*');

        self::assertTrue($accept->allows('text/plain'));
        self::assertTrue($accept->allows('text/html'));
        self::assertTrue($accept->allows('audio/wav'));

        self::assertFalse($accept->allows('text/csv'));
        self::assertFalse($accept->allows('application/*'));
        self::assertFalse($accept->allows('*/*'));
    }

    public function testCanCastHeaderToString(): void
    {
        self::assertSame(
            'text/plain;q=1,text/html,audio/*',
            (string) $this->accept,
        );
    }

    /**
     * @covers \Swiftly\Http\Exception\HeaderException
     */
    public function testThrowsIfAppliedToHttpResponse(): void
    {
        self::expectException(HeaderException::class);
        self::expectExceptionMessage('Could not apply the "Accept" header');

        $response = $this->createMock(Response::class);

        $this->accept->applyTo($response);
    }
}
