<?php

namespace Swiftly\Http\Tests;

use Swiftly\Http\Session;
use Swiftly\Http\SessionInterface;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
Class SessionTest Extends TestCase
{

    public function testCanStartSession() : array
    {
        $adapter = $this->createMock( SessionInterface::class );
        $session = new Session( $adapter );

        $adapter->expects( $this->once() )
            ->method( 'open' )
            ->willReturn( true );

        $session->open();

        self::assertTrue( $session->isOpen() );

        return [$session, $adapter];
    }

    /**
     * @depends testCanStartSession
     */
    public function testCanStopSession(array $open) : void
    {
        list($session, $adapter) = $open;

        $adapter->expects( $this->once() )
            ->method( 'close' )
            ->willReturn( true );

        $session->close();

        self::assertFalse( $session->isOpen() );
    }
}
