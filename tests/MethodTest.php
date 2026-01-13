<?php declare(strict_types=1);

namespace Swiftly\Http\Tests;

use PHPUnit\Framework\TestCase;
use Swiftly\Http\Method;

/**
 * @covers \Swiftly\Http\Method
 */
final class MethodTest extends TestCase
{
    public function exampleMethodProvider(): array
    {
        /** [Method:string, Known:bool, Safe:bool, Cacheable:bool] */
        return [
            'GET' => ['GET', true, true, true],
            'HEAD' => ['HEAD', true, true, true],
            'POST' => ['POST', true, false, false],
            'PUT' => ['PUT', true, false, false],
            'DELETE' => ['DELETE', true, false, false],
            'CONNECT' => ['CONNECT', true, false, false],
            'OPTIONS' => ['OPTIONS', true, true, false],
            'TRACE' => ['TRACE', true, true, false],
            'PATCH' => ['PATCH', false, false, false],
            'CUSTOM' => ['CUSTOM', false, false, false]
        ];
    }

    /** @dataProvider exampleMethodProvider */
    public function testCanTellIfMethodIsKnown(string $method, bool $known): void
    {
        self::assertSame($known, Method::isKnownMethod($method));
    }

    /** @dataProvider exampleMethodProvider */
    public function testCanTellIfMethodIsSafe(string $method, $_, bool $safe): void
    {
        self::assertSame($safe, Method::isSafeMethod($method));
    }

    /** @dataProvider exampleMethodProvider */
    public function testCanTellIfMethodAllowsCachedResponses(string $method, $_1, $_2, bool $cacheable): void
    {
        self::assertSame($cacheable, Method::isCacheableMethod($method));
    }
}
