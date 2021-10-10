<?php

namespace Swiftly\Http\Tests;

use Swiftly\Http\Session;
use Swiftly\Http\SessionInterface;
use PHPUnit\Framework\TestCase;

/**
 * @group Shared
 */
Class SessionTest Extends TestCase
{

    /** @var Session $session */
    private $session;

    protected function setUp() : void
    {

    }

    public function testCanStartSession() : void
    {
        $adapter = $this->createMock( SessionInterface::class );

        $adapter->expects( $this->once() )
            ->method( 'open' )
            ->willReturn( true );

        $session = new Session( $adapter );
        $session->open();

        self::assertTrue( $session->isOpen() );
    }

    public function testCanStopSession() : void
    {
        $adapter = $this->createMock( SessionInterface::class );

        $adapter->expects( $this->once() )
            ->method( 'close' )
            ->willReturn( true );

        $session = new Session( $adapter );
        $session->close();

        self::assertFalse( $session->isOpen() );
    }

    // TODO:
}
