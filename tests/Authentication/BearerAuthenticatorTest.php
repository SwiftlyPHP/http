<?php

namespace Swiftly\Http\Tests\Authentication;

use Swiftly\Http\Authentication\BearerAuthenticator;
use Swiftly\Http\Client\Request;
use PHPUnit\Framework\TestCase;

/**
 * @group Shared
 */
Class BearerAuthenticatorTest Extends TestCase
{

    /** @var BearerAuthenticator $authenticator */
    private $authenticator;

    /** @var Request $request */
    private $request;

    protected function setUp() : void
    {
        $this->authenticator = new BearerAuthenticator( 'some_api_token' );
        $this->request = new Request();
    }

    public function testSetsCorrectHttpHeader() : void
    {
        $this->authenticator->authenticate( $this->request );

        self::assertTrue( $this->request->headers->has( 'Authorization' ) );
        self::assertSame( "Bearer some_api_token",
            $this->request->headers->get( 'Authorization' )
        );
    }
}
