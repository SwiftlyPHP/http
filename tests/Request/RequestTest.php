<?php

namespace Swiftly\Http\Tests\Request;

use PHPUnit\Framework\TestCase;
use Swiftly\Http\Request\Request;
use Swiftly\Http\HeaderCollection;
use Swiftly\Http\CookieCollection;
use Swiftly\Http\ParameterCollection;
use Swiftly\Http\SessionHandler;
use Swiftly\Http\SessionStorageInterface;
use Swiftly\Http\RequestAwareSessionInterface;
use Swiftly\Http\Exception\SessionException;
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
 * @uses \Swiftly\Http\SessionHandler
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

    public function testCanGetSession(): void
    {
        $this->request->setSession($this->createMock(SessionHandler::class));

        self::assertInstanceOf(SessionHandler::class, $this->request->getSession());
    }

    public function testCanCheckIfRequestHasSession(): void
    {
        self::assertFalse($this->request->hasSession());
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

    public function testCanAttachSessionHandler(): void
    {
        $session = $this->createMock(SessionHandler::class);
        $session
            ->expects(self::once())
            ->method('attach')
            ->with($this->request);

        $this->request->setSession($session);
    }

    public function testCanAttachSessionStorage(): void
    {
        // https://github.com/sebastianbergmann/phpunit/issues/3955
        $store = $this->createMock(RequestAwareSessionMock::class);
        $store
            ->expects(self::once())
            ->method('setRequest')
            ->with($this->request);

        $this->request->setSession($store);
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

    /** @covers \Swiftly\Http\Exception\SessionException */
    public function testThrowsIfSessionNotSet(): void
    {
        self::expectException(SessionException::class);
        self::expectExceptionMessageMatches('/no attached session/');

        $this->request->getSession();
    }

    /** @covers \Swiftly\Http\Exception\SessionException */
    public function testThrowsIfSessionAlreadySet(): void
    {
        self::expectException(SessionException::class);
        self::expectExceptionMessageMatches('/has attached session/');
        
        self::assertFalse($this->request->hasSession());
        $this->request->setSession($this->createMock(SessionHandler::class));
        
        self::assertTrue($this->request->hasSession());
        $this->request->setSession($this->createMock(SessionHandler::class));
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

// https://github.com/sebastianbergmann/phpunit/issues/3955
abstract class RequestAwareSessionMock implements
    SessionStorageInterface,
    RequestAwareSessionInterface {}
