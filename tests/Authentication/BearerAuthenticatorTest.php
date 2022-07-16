<?php

namespace Swiftly\Http\Tests\Authentication;

use Swiftly\Http\Authentication\BearerAuthenticator;
use Swiftly\Http\Client\Request;
use Swiftly\Http\Headers;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
Class BearerAuthenticatorTest Extends TestCase
{
    public function testCanSetHeaderValue() : void
    {
        $request = $this->createMock(Request::class);
        $headers = $this->createMock(Headers::class);
        $headers->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo('Authorization'),
                $this->equalTo('Bearer MY_COOL_API_KEY')
            );

        $request->headers = $headers;

        $auth = new BearerAuthenticator('MY_COOL_API_KEY');
        $auth->authenticate($request);
    }
}
