<?php

namespace Swiftly\Http\Tests\Authentication;

use Swiftly\Http\Authentication\BasicAuthenticator;
use Swiftly\Http\Client\Request;
use PHPUnit\Framework\TestCase;

use function base64_encode;

/**
 * @group Shared
 */
Class BasicAuthenticatorTest Extends TestCase
{

    /** @var BasicAuthenticator $authenticator */
    private $authenticator;

    /** @var Request $request */
    private $request;

    protected function setUp() : void
    {
        $this->authenticator = new BasicAuthenticator( 'username', 'password' );
        $this->request = new Request();
    }

    public function testSetsCorrectHttpHeader() : void
    {
        $this->authenticator->authenticate( $this->request );

        $expected = base64_encode( 'username:password' );

        self::assertTrue( $this->request->headers->has( 'Authorization' ) );
        self::assertSame( "Basic $expected",
            $this->request->headers->get( 'Authorization' )
        );
    }
}
