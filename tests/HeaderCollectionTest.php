<?php

namespace Swiftly\Http\Tests;

use PHPUnit\Framework\TestCase;
use Swiftly\Http\HeaderCollection;

/**
 * @covers \Swiftly\Http\HeaderCollection
 */
final class HeaderCollectionTest extends TestCase
{
    private HeaderCollection $headers;

    public function setUp(): void
    {
        $this->headers = new HeaderCollection([
            'Accept' => 'text/html',
            'Accept-Language' => 'en-GB',
            'User-Agent' => 'Mozilla/5.0'
        ]);
    }

    public function testCanCheckIfHeaderExists(): void
    {
        self::assertTrue($this->headers->has('Accept'));
        self::assertTrue($this->headers->has('Accept-Language'));
        self::assertTrue($this->headers->has('User-Agent'));
        self::assertFalse($this->headers->has('Dnt'));
        self::assertFalse($this->headers->has('Pragma'));
        self::assertFalse($this->headers->has('Cache-Control'));
    }

    public function testCanCheckIfHeaderExistsCaseInsensitive(): void
    {
        self::assertTrue($this->headers->has('accept'));
        self::assertTrue($this->headers->has('ACCEPT-LANGUAGE'));
        self::assertTrue($this->headers->has('uSER-aGENT'));
        self::assertFalse($this->headers->has('dnt'));
        self::assertFalse($this->headers->has('PRAGMA'));
        self::assertFalse($this->headers->has('cACHE-cONTROL'));
    }

    public function testCanGetHeaderValue(): void
    {
        self::assertSame('text/html', $this->headers->get('Accept'));
        self::assertSame('en-GB', $this->headers->get('Accept-Language'));
        self::assertSame('Mozilla/5.0', $this->headers->get('User-Agent'));
        self::assertNull($this->headers->get('Dnt'));
        self::assertNull($this->headers->get('Pragma'));
        self::assertNull($this->headers->get('Cache-Control'));
    }

    public function testCanGetHeaderValueCaseInsensitive(): void
    {
        self::assertSame('text/html', $this->headers->get('accept'));
        self::assertSame('en-GB', $this->headers->get('ACCEPT-LANGUAGE'));
        self::assertSame('Mozilla/5.0', $this->headers->get('uSER-aGENT'));
        self::assertNull($this->headers->get('dnt'));
        self::assertNull($this->headers->get('PRAGMA'));
        self::assertNull($this->headers->get('cACHE-cONTROL'));
    }

    public function testCanGetAllValuesForNamedHeader(): void
    {
        self::assertSame(['text/html'], $this->headers->all('Accept'));
        self::assertSame(['en-GB'], $this->headers->all('Accept-Language'));
        self::assertSame(['Mozilla/5.0'], $this->headers->all('User-Agent'));
        self::assertNull($this->headers->all('Dnt'));
        self::assertNull($this->headers->all('Pragma'));
        self::assertNull($this->headers->all('Cache-Control'));
    }

    public function testCanGetAllValuesForNamedHeaderCaseInsensitive(): void
    {
        self::assertSame(['text/html'], $this->headers->all('accept'));
        self::assertSame(['en-GB'], $this->headers->all('ACCEPT-LANGUAGE'));
        self::assertSame(['Mozilla/5.0'], $this->headers->all('uSER-aGENT'));
        self::assertNull($this->headers->all('dnt'));
        self::assertNull($this->headers->all('PRAGMA'));
        self::assertNull($this->headers->all('cACHE-cONTROL'));
    }

    public function testCanGetAllHeaders(): void
    {
        self::assertSame([
            'accept' => ['text/html'],
            'accept-language' => ['en-GB'],
            'user-agent' => ['Mozilla/5.0']
        ], $this->headers->all());
    }

    public function testCanSetNewHeaderValue(): void
    {
        self::assertFalse($this->headers->has('Accept-Encoding'));

        $this->headers->set('Accept-Encoding', 'gzip');

        self::assertTrue($this->headers->has('Accept-Encoding'));
    }

    public function testCanReplaceExistingHeaderValue(): void
    {
        self::assertSame('text/html', $this->headers->get('Accept'));

        $this->headers->set('Accept', 'application/json');

        self::assertSame('application/json', $this->headers->get('Accept'));
    }

    public function testCanAppendHeaderValue(): void
    {
        self::assertSame(['text/html'], $this->headers->all('Accept'));

        $this->headers->set('Accept', 'text/plain', false);

        self::assertSame([
            'text/html', 'text/plain'
        ], $this->headers->all('Accept'));
    }
}
