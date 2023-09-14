<?php

namespace Swiftly\Http\Tests\Request;

use PHPUnit\Framework\TestCase;
use Swiftly\Http\Request\Request;
use Swiftly\Http\HeaderCollection;
use Swiftly\Http\CookieCollection;
use Swiftly\Http\ParameterCollection;
use Swiftly\Http\Exception\UrlParseException;
use Swiftly\Http\Exception\EnvironmentException;

use function array_merge;

/**
 * @covers \Swiftly\Http\Request\Request
 * @uses \Swiftly\Http\Url
 * @uses \Swiftly\Http\HeaderCollection
 * @uses \Swiftly\Http\CookieCollection
 * @uses \Swiftly\Http\ParameterCollection
 * @uses \Swiftly\Http\Helpers
 * @uses \Swiftly\Http\Method
 */
final class RequestTest extends TestCase
{
    private Request $request;

    public function setUp(): void
    {
        $this->request = Request::create(
            'GET',
            'http://localhost/resource/sub-resource',
            ['Accept' => 'text/html', 'Cache-Control' => 'no-store'],
            [],
            []
        );
    }

    /** These properties are part of the API - they must be public */
    public function testRequiredPropertiesArePublic(): void
    {
        self::assertInstanceOf(HeaderCollection::class, $this->request->headers);
        self::assertInstanceOf(CookieCollection::class, $this->request->cookies);
        self::assertInstanceOf(ParameterCollection::class, $this->request->query);
        self::assertInstanceOf(ParameterCollection::class, $this->request->post);
    }

    public function testCanGetHttpMethod(): void
    {
        self::assertSame('GET', $this->request->getMethod());
    }

    public function testCanGetProtocol(): void
    {
        self::assertSame('http', $this->request->getProtocol());
    }

    public function testCanGetRequestedPath(): void
    {
        self::assertSame('/resource/sub-resource', $this->request->getPath());
    }

    public function testCanCheckIfRequestWasOverHttps(): void
    {
        self::assertFalse($this->request->isSecure());
    }

    public function testCanCheckIfUsingKnownHttpMethod(): void
    {
        self::assertTrue($this->request->isKnownMethod());
    }

    public function testCanCheckIfUsingSafeHttpMethod(): void
    {
        self::assertTrue($this->request->isSafeMethod());
    }

    public function testCanCheckIfHttpMethodAllowsCaching(): void
    {
        self::assertTrue($this->request->allowsCachedResponses());
    }

    /** @backupGlobals enabled */
    public function testCanCreateRequestFromGlobals(): void
    {
        $_SERVER = array_merge($_SERVER, [
            'REQUEST_METHOD' => 'POST',
            'HTTPS' => true,
            'HTTP_HOST' => 'example.com',
            'REQUEST_URI' => '/account/delete'
        ]);

        $request = Request::fromGlobals();

        self::assertSame('POST', $request->getMethod());
        self::assertSame('https', $request->getProtocol());
        self::assertSame('/account/delete', $request->getPath());
        self::assertTrue($request->isSecure());
        self::assertTrue($request->isKnownMethod());
        self::assertFalse($request->isSafeMethod());
        self::assertFalse($request->allowsCachedResponses());
    }

    /** @covers \Swiftly\Http\Exception\UrlParseException */
    public function testThrowsIfInvalidUrlProvided(): void
    {
        self::expectException(UrlParseException::class);
        self::expectExceptionMessageMatches('/Failed to parse/');

        Request::create('GET', 'http:\\my@url');
    }

    /** @covers \Swiftly\Http\Exception\EnvironmentException */
    public function testThrowsIfGlobalVariablesMissing(): void
    {
        self::expectException(EnvironmentException::class);
        self::expectExceptionMessageMatches('/\$_SERVER/');

        Request::fromGlobals();
    }
}
