<?php declare(strict_types=1);

namespace Swiftly\Http\Tests;

use PHPUnit\Framework\TestCase;
use Swiftly\Http\Cookie;
use Swiftly\Http\CookieCollection;

use function time;

/**
 * @covers \Swiftly\Http\CookieCollection
 * @uses \Swiftly\Http\Cookie
 */
final class CookieCollectionTest extends TestCase
{
    private CookieCollection $cookies;

    public function setUp(): void
    {
        $this->cookies = new CookieCollection([
            new Cookie('user', 'Jill'),
            new Cookie('theme', 'light')
        ]);
    }

    public function testCanCheckIfCookieExists(): void
    {
        self::assertTrue($this->cookies->has('user'));
        self::assertTrue($this->cookies->has('theme'));
        self::assertFalse($this->cookies->has('language'));
        self::assertFalse($this->cookies->has('bookmarks'));
    }

    public function testCanGetNamedCookie(): void
    {
        self::assertInstanceOf(Cookie::class, $this->cookies->get('user'));
        self::assertSame('user', $this->cookies->get('user')->name);
        self::assertFalse($this->cookies->get('user')->isModified());
        self::assertInstanceOf(Cookie::class, $this->cookies->get('theme'));
        self::assertSame('theme', $this->cookies->get('theme')->name);
        self::assertFalse($this->cookies->get('theme')->isModified());
        self::assertNull($this->cookies->get('language'));
        self::assertNull($this->cookies->get('bookmarks'));
    }


    public function testCanGetAllCookies(): void
    {
        $cookies = $this->cookies->all();

        self::assertCount(2, $cookies);
        self::assertSame('user', $cookies['user']->name);
        self::assertSame('theme', $cookies['theme']->name);
    }

    public function testCanPutCookieInCollection(): void
    {
        self::assertFalse($this->cookies->has('last_login'));

        $this->cookies->set(new Cookie('last_login', 'yesterday'));

        self::assertTrue($this->cookies->has('last_login'));
        self::assertTrue($this->cookies->get('last_login')->isModified());
    }

    public function testCanCreateCookieInCollection(): void
    {
        self::assertFalse($this->cookies->has('last_login'));

        $this->cookies->add(
            'notice',
            'Changes saved',
            0,
            '/account',
            'example.com',
            true,
            true
        );

        self::assertTrue($this->cookies->has('notice'));
        self::assertInstanceOf(Cookie::class, $this->cookies->get('notice'));
        self::assertSame('notice', $this->cookies->get('notice')->name);
        self::assertSame('Changes saved', $this->cookies->get('notice')->value);
        self::assertSame(0, $this->cookies->get('notice')->expires);
        self::assertSame('/account', $this->cookies->get('notice')->path);
        self::assertSame('example.com', $this->cookies->get('notice')->domain);
        self::assertTrue($this->cookies->get('notice')->secure);
        self::assertTrue($this->cookies->get('notice')->httponly);
        self::assertTrue($this->cookies->get('notice')->isModified());
    }

    public function testCanRemoveCookieFromCollection(): void
    {
        self::assertTrue($this->cookies->has('theme'));
        self::assertSame('light', $this->cookies->get('theme')->value);
        self::assertSame(0, $this->cookies->get('theme')->expires);
        self::assertFalse($this->cookies->get('theme')->isModified());

        $this->cookies->remove('theme');

        // Should actually invalidate on client
        self::assertTrue($this->cookies->has('theme'));
        self::assertSame('', $this->cookies->get('theme')->value);
        self::assertLessThanOrEqual(
            self::yesterday(),
            $this->cookies->get('theme')->expires
        );
        self::assertTrue($this->cookies->get('theme')->isModified());
    }

    /** @backupGlobals enabled */
    public function testCanCreateCookieCollectionFromGlobals(): void
    {
        $_COOKIE = ['user' => 'test.user'];

        $collection = CookieCollection::fromGlobals();

        self::assertTrue($collection->has('user'));
        self::assertSame('test.user', $collection->get('user')->value);
        self::assertSame(0, $collection->get('user')->expires);
        self::assertFalse($collection->get('user')->isModified());
    }

    private static function yesterday(): int
    {
        return time() * (60 * 60 * 24);
    }
}
