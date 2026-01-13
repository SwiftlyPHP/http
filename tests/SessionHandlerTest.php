<?php

namespace Swiftly\Http\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Swiftly\Http\SessionHandler;
use Swiftly\Http\SessionStorageInterface;
use Swiftly\Http\RequestAwareSessionInterface;
use Swiftly\Http\Request\Request;
use Swiftly\Http\Exception\SessionException;

/**
 * @covers \Swiftly\Http\SessionHandler
 */
final class SessionHandlerTest extends TestCase
{
    /** @var RequestAwareSessionMock&MockObject $storage */
    private SessionStorageInterface $storage;
    private SessionHandler $session;

    public function setUp(): void
    {
        $this->session = new SessionHandler(
            $this->storage = $this->createMock(RequestAwareSessionMock::class)
        );
    }

    /** @testdox Calling ::has() opens session */
    public function testCallingHasOpensSession(): void
    {
        self::assertFalse($this->session->isOpen());

        $this->session->has('foo');

        self::assertTrue($this->session->isOpen());
    }

    /** @testdox Calling ::get() opens session */
    public function testCallingGetOpensSession(): void
    {
        self::assertFalse($this->session->isOpen());

        $this->session->get('foo');
        
        self::assertTrue($this->session->isOpen());
    }

    /** @testdox Calling ::set() opens session */
    public function testCallingSetOpensSession(): void
    {
        self::assertFalse($this->session->isOpen());

        $this->session->set('foo', 'bar');
        
        self::assertTrue($this->session->isOpen());
    }

    /** @testdox Calling ::remove() opens session */
    public function testCallingRemoveOpensSession(): void
    {
        self::assertFalse($this->session->isOpen());

        $this->session->remove('foo');
        
        self::assertTrue($this->session->isOpen());
    }

    /** @testdox Calling ::clear() opens session */
    public function testCallingClearOpensSession(): void
    {
        self::assertFalse($this->session->isOpen());

        $this->session->clear();
        
        self::assertTrue($this->session->isOpen());
    }

    public function testCanTellIfSessionIsOpen(): void
    {
        self::assertFalse($this->session->isOpen());

        $this->session->open();

        self::assertTrue($this->session->isOpen());
    }

    public function testCanTellIfSessionIsClosed(): void
    {
        self::assertFalse($this->session->isClosed());

        $this->session->open();

        self::assertFalse($this->session->isClosed());

        $this->session->close();

        self::assertTrue($this->session->isClosed());
    }

    public function testCanCheckIfValueInSession(): void
    {
        $this->session->open();

        $this->storage->expects(self::once())
            ->method('has')
            ->with('foo')
            ->willReturn(true);

        self::assertTrue($this->session->has('foo'));
    }

    public function testCanGetValueFromSession(): void
    {
        $this->session->open();

        $this->storage->expects(self::once())
            ->method('read')
            ->with('foo')
            ->willReturn('bar');

        self::assertSame('bar', $this->session->get('foo'));
    }

    public function testCanSetValueInSession(): void
    {
        $this->session->open();

        $this->storage->expects(self::once())
            ->method('write')
            ->with('foo', 'bar');

        $this->session->set('foo', 'bar');
    }

    public function testCanRemoveValueFromSession(): void
    {
        $this->session->open();

        $this->storage->expects(self::once())
            ->method('remove')
            ->with('foo');

        $this->session->remove('foo');
    }

    public function testCanClearValuesFromSession(): void
    {
        $this->session->open();

        $this->storage->expects(self::once())
            ->method('clear');

        $this->session->clear();
    }

    public function testCanDestroySession(): void
    {
        $this->storage->expects(self::once())
            ->method('destroy');

        $this->session->destroy();
    }

    public function testCanAttachRequestToSession(): void
    {
        $request = $this->createMock(Request::class);

        $this->storage->expects(self::once())
            ->method('setRequest')
            ->with($request);

        $this->session->attach($request);
    }

    public function testClosesSessionOnDestruction(): void
    {
        $this->session->open();

        $this->storage->expects(self::once())->method('close');

        unset($this->session);
    }

    /**
     * @covers \Swiftly\Http\Exception\SessionException
     */
    public function testThrowsReadExceptionWhenAlreadyClosed(): void
    {
        $this->session->open();
        $this->session->close();

        self::expectException(SessionException::class);
        self::expectExceptionMessageMatches('/is already closed/');

        $this->session->get('foo');
    }

    /**
     * @covers \Swiftly\Http\Exception\SessionException
     */
    public function testThrowsWriteExceptionWhenAlreadyClosed(): void
    {
        $this->session->open();
        $this->session->close();

        self::expectException(SessionException::class);
        self::expectExceptionMessageMatches('/is already closed/');

        $this->session->set('foo', 'bar');
    }

    /** @covers \Swiftly\Http\Exception\SessionException */
    public function testThrowsIfTryingToOpenWhenAlreadyOpen(): void
    {
        $this->session->open();

        self::expectException(SessionException::class);
        self::expectExceptionMessageMatches('/is already open/');

        $this->session->open();
    }

    /** @covers \Swiftly\Http\Exception\SessionException */
    public function testThrowsIfTryingToOpenWhenClosed(): void
    {
        $this->session->open();
        $this->session->close();

        self::expectException(SessionException::class);
        self::expectExceptionMessageMatches('/has already been closed/');

        $this->session->open();
    }

    /** @covers \Swiftly\Http\Exception\SessionException */
    public function testThrowsIfTryingToCloseWhenUnopened(): void
    {
        self::expectException(SessionException::class);
        self::expectExceptionMessageMatches('/was never opened/');

        $this->session->close();
    }

    /** @covers \Swiftly\Http\Exception\SessionException */
    public function testThrowsIfTryingToCloseWhenAlreadyClosed(): void
    {
        $this->session->open();
        $this->session->close();

        self::expectException(SessionException::class);
        self::expectExceptionMessageMatches('/is already closed/');

        $this->session->close();
    }
}

// https://github.com/sebastianbergmann/phpunit/issues/3955
abstract class RequestAwareSessionMock implements
    SessionStorageInterface,
    RequestAwareSessionInterface {}
