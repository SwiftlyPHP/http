<?php

namespace Swiftly\Http\Tests;

use PHPUnit\Framework\TestCase;
use Swiftly\Http\Cookie;

use function time;

/**
 * @covers \Swiftly\Http\Cookie
 */
final class CookieTest extends TestCase
{
    private Cookie $cookie;

    public function setUp(): void
    {
        $this->cookie = new Cookie('id', 'user_1');
    }

    public function testCanInvalidateCookie(): void
    {
        $this->cookie->invalidate();

        $yesterday = time() - (60 * 60 * 24);

        self::assertLessThanOrEqual($yesterday, $this->cookie->expires);
        self::assertSame('', $this->cookie->value);
    }

    public function testIsHttpsByDefault(): void
    {
        self::assertTrue($this->cookie->secure);
    }
}
