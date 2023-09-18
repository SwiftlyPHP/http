<?php

namespace Swiftly\Http\Tests\Session;

use PHPUnit\Framework\TestCase;
use Swiftly\Http\Session\NativeSession;
use Swiftly\Http\RequestAwareSessionInterface;
use Swiftly\Http\Request\Request;
use Swiftly\Http\CookieCollection;
use Swiftly\Http\Exception\SessionException;

use function session_status;
use function array_merge;
use function error_reporting;

use const PHP_SESSION_NONE;
use const PHP_SESSION_ACTIVE;

/**
 * @covers \Swiftly\Http\Session\NativeSession
 * @backupGlobals enable
 * @runTestsInSeparateProcesses
 */
final class NativeSessionTest extends TestCase
{
    private NativeSession $session;

    public function setUp(): void
    {
        $this->session = new NativeSession();
    }

    public function testCanGetSessionName(): void
    {
        self::assertSame('PHPSESSID', $this->session->name());
    }

    public function testCanOpenSession(): void
    {
        self::assertSame(PHP_SESSION_NONE, session_status());

        $this->session->open();

        self::assertSame(PHP_SESSION_ACTIVE, session_status());
    }

    public function testCanCloseSession(): void
    {
        $this->session->open();
        
        self::assertSame(PHP_SESSION_ACTIVE, session_status());

        $this->session->close();

        self::assertSame(PHP_SESSION_NONE, session_status());
    }

    public function testCanCheckIfValueInSession(): void
    {
        $this->session->open();
        $_SESSION['foo'] = 'bar';

        self::assertTrue($this->session->has('foo'));
        self::assertFalse($this->session->has('baz'));
    }

    public function testCanGetValueFromSession(): void
    {
        $this->session->open();
        $_SESSION['foo'] = 'bar';

        self::assertSame('bar', $this->session->read('foo'));
    }

    public function testCanSetValueInSession(): void
    {
        $this->session->open();

        self::assertFalse($this->session->has('bar'));

        $this->session->write('bar', 'baz');

        self::assertTrue($this->session->has('bar'));
        self::assertSame('baz', $this->session->read('bar'));
    }

    public function testCanRemoveValueFromSession(): void
    {
        $this->session->open();
        $_SESSION['foo'] = 'bar';

        self::assertTrue($this->session->has('foo'));

        $this->session->remove('foo');

        self::assertFalse($this->session->has('foo'));
    }

    public function testCanClearValuesFromSession(): void
    {
        $this->session->open();
        $_SESSION = array_merge([
            'foo' => 'bar',
            'baz' => 'biz'
        ], $_SESSION);

        self::assertTrue($this->session->has('foo'));
        self::assertTrue($this->session->has('baz'));

        $this->session->clear();

        self::assertFalse($this->session->has('foo'));
        self::assertFalse($this->session->has('baz'));
    }

    public function testCanDestroySession(): void
    {
        $this->session->open();
        $_SESSION = array_merge([
            'foo' => 'bar',
            'baz' => 'biz'
        ], $_SESSION);

        self::assertTrue($this->session->has('foo'));
        self::assertTrue($this->session->has('baz'));

        $this->session->destroy();

        self::assertFalse($this->session->has('foo'));
        self::assertFalse($this->session->has('baz'));
    }

    public function testCanPersistSession(): void
    {
        $this->session->open();
        $this->session->write('foo', 'bar');
        // Persist here is no-op but we want to keep session tests uniform
        $this->session->persist();
        $this->session->close();

        $this->session->open();
        self::assertTrue($this->session->has('foo'));
        self::assertSame('bar', $this->session->read('foo'));
    }

    public function testIsRequestAware(): void
    {
        self::assertInstanceOf(
            RequestAwareSessionInterface::class,
            $this->session
        );
    }

    /** @depends testIsRequestAware */
    public function testCanDeleteSessionCookieFromRequest(): void
    {
        $this->session->open();
        
        $cookies = $this->createMock(CookieCollection::class);
        $cookies->expects(self::once())
            ->method('remove')
            ->with($this->session->name());
        $request = $this->createMock(Request::class);
        $request->cookies = $cookies;

        $this->session->setRequest($request);
        $this->session->destroy();
    }

    public function testThrowsIfCannotOpenSession(): void
    {
        self::expectException(SessionException::class);
        self::expectExceptionMessageMatches('/already active/');

        // Manually cause 'session already active' error
        $this->session->open();
        $this->session->open();
    }
}
