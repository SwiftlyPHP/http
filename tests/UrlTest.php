<?php declare(strict_types=1);

namespace Swiftly\Http\Tests;

use PHPUnit\Framework\TestCase;
use Swiftly\Http\Exception\EnvironmentException;
use Swiftly\Http\Exception\UrlParseException;
use Swiftly\Http\Url;

use function array_merge;
use function preg_quote;

/**
 * @covers \Swiftly\Http\Url
 */
final class UrlTest extends TestCase
{
    public function exampleUrlProvider(): array
    {
        /**
         * [
         *   Url:non-empty-string,
         *   Protocol:non-empty-string,
         *   Domain:non-empty-string,
         *   Path:non-empty-string,
         *   Query:string,
         *   Fragment:string
         * ]
         */
        return [
            [
                'https://example.com/resource?page=1#section',
                'https', 'example.com', '/resource', 'page=1', 'section'
            ],
            [
                'http://foo.com/content/articles?author=John',
                'http', 'foo.com', '/content/articles', 'author=John', ''
            ],
            [
                'https://example.co.uk/#heading',
                'https', 'example.co.uk', '/', '', 'heading'
            ],
            [
                'https://google.com/',
                'https', 'google.com', '/', '', ''
            ],
            [
                'http://localhost/?title=test&page=1',
                'http', 'localhost', '/', 'title=test&page=1', ''
            ]
        ];
    }

    /** @dataProvider exampleUrlProvider */
    public function testCanParseUrlFromString(
        string $example,
        string $protocol,
        string $domain,
        string $path,
        string $query,
        string $fragment
    ): void {
        $url = Url::fromString($example);

        self::assertSame($protocol, $url->protocol);
        self::assertSame($domain, $url->domain);
        self::assertSame($path, $url->path);
        self::assertSame($query, $url->query);
        self::assertSame($fragment, $url->fragment);
        self::assertSame($example, (string)$url);
    }

    /** @dataProvider exampleUrlProvider */
    public function testCanCastUrlIntoString(
        string $example,
        string $protocol,
        string $domain,
        string $path,
        string $query,
        string $fragment
    ): void {
        $url = new Url($protocol, $domain, $path, $query, $fragment);

        self::assertSame($example, (string) $url);
    }

    /** @backupGlobals enabled */
    public function testCanCreateUrlFromGlobals(): void
    {
        $_SERVER = array_merge($_SERVER, [
            'HTTP_HOST' => 'example.com',
            'REQUEST_URI' => '/resource/sub-resource?foo=bar#id'
        ]);

        $url = Url::fromGlobals();

        self::assertSame('http', $url->protocol);
        self::assertSame('example.com', $url->domain);
        self::assertSame('/resource/sub-resource', $url->path);
        self::assertSame('foo=bar', $url->query);
        self::assertSame('id', $url->fragment);
    }

    /** @backupGlobals enabled */
    public function testCanTellIfHttpsFromGlobals(): void
    {
        $_SERVER = array_merge($_SERVER, [
            'HTTP_HOST' => 'example.com',
            'REQUEST_URI' => '/resource/sub-resource?foo=bar#id',
            'HTTPS' => true
        ]);

        $url = Url::fromGlobals();

        self::assertSame('https', $url->protocol);
    }

    /** @backupGlobals enabled */
    public function testCanTellIfHttpsBehindProxyFromGlobals(): void
    {
        $_SERVER = array_merge($_SERVER, [
            'HTTP_HOST' => 'example.com',
            'REQUEST_URI' => '/resource/sub-resource?foo=bar#id',
            'HTTP_X_FORWARDED_PROTO' => 'https'
        ]);

        $url = Url::fromGlobals();

        self::assertSame('https', $url->protocol);
    }

    /** @covers \Swiftly\Http\Exception\UrlParseException */
    public function testThrowsIfInvalidStringProvided(): void
    {
        self::expectException(UrlParseException::class);
        self::expectExceptionMessageMatches('/invalid format/');

        Url::fromString('https://#@invalid?url');
    }

    /** @covers \Swiftly\Http\Exception\UrlParseException */
    public function testThrowsIfHostnameMissing(): void
    {
        self::expectException(UrlParseException::class);
        self::expectExceptionMessageMatches('/hostname/');

        Url::fromString('/?missing=domain');
    }

    /** @covers \Swiftly\Http\Exception\UrlParseException */
    public function testThrowsIfPathMissing(): void
    {
        self::expectException(UrlParseException::class);
        self::expectExceptionMessageMatches('/path/');

        Url::fromString('https://example.com#fragment');
    }

    /** @covers \Swiftly\Http\Exception\EnvironmentException */
    public function testThrowsIfGlobalHostNameMissing(): void
    {
        self::expectException(EnvironmentException::class);
        self::expectExceptionMessageMatches(
            '/' . preg_quote("\$_SERVER['HTTP_HOST'] is required") . '/',
        );

        Url::fromGlobals();
    }

    /**
     * @backupGlobals enabled
     * @covers \Swiftly\Http\Exception\EnvironmentException
     */
    public function testThrowsIfGlobalRequestUriMissing(): void
    {
        $_SERVER = array_merge($_SERVER, ['HTTP_HOST' => 'example.com']);

        self::expectException(EnvironmentException::class);
        self::expectExceptionMessageMatches(
            '/' . preg_quote("\$_SERVER['REQUEST_URI'] is required") . '/',
        );

        Url::fromGlobals();
    }
}
