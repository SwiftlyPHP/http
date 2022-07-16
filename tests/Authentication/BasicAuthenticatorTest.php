<?php

namespace Swiftly\Http\Tests\Authentication;

use Swiftly\Http\Authentication\BasicAuthenticator;
use Swiftly\Http\Client\Request;
use Swiftly\Http\Headers;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
Class BasicAuthenticatorTest Extends TestCase
{
    public function testCanSetHeaderValue() : void
    {
        $request = $this->createMock(Request::class);
        $headers = $this->createMock(Headers::class);
        $headers->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo('Authorization'),
                $this->equalTo('Basic bXlfdXNlcm5hbWU6bXlfcGFzc3dvcmQ=')
            );

        $request->headers = $headers;

        $auth = new BasicAuthenticator('my_username', 'my_password');
        $auth->authenticate($request);
    }
}
