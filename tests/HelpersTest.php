<?php

namespace Swiftly\Http\Tests;

use PHPUnit\Framework\TestCase;
use Swiftly\Http\Helpers;

use function array_merge;
use function array_keys;
use function error_reporting;

/**
 * @covers \Swiftly\Http\Helpers
 * @backupGlobals enabled
 */
final class HelpersTest extends TestCase
{
    private const PascalCase = '/^(?:[A-Z][a-z]*)(?:-[A-Z][a-z]*)*$/';

    private array $headers;

    public function setUp(): void
    {
        $_SERVER = array_merge($_SERVER, [
            'HTTP_ACCEPT' => 'text/html,text/plain',
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
            'HTTP_CONNECTION' => 'keep-alive',
            'HTTP_DNT' => '1',
            'HTTP_X_POWERED_BY' => 'Swiftly',
            'HTTP_' => 'should-not-appear'
        ]);

        $this->headers = Helpers::getHeaders();
    }

    public function testCanFetchHttpHeaders(): void
    {
        // Check headers are Pascal-Case
        foreach (array_keys($this->headers) as $name) {
            self::assertMatchesRegularExpression(self::PascalCase, $name);
        }

        self::assertArrayHasKey('Accept', $this->headers);
        self::assertArrayHasKey('Accept-Language', $this->headers);
        self::assertArrayHasKey('Connection', $this->headers);
        self::assertArrayHasKey('Dnt', $this->headers);
        self::assertArrayHasKey('X-Powered-By', $this->headers);
        
        self::assertSame('text/html,text/plain', $this->headers['Accept']);
        self::assertSame('en-GB', $this->headers['Accept-Language']);
        self::assertSame('keep-alive', $this->headers['Connection']);
        self::assertSame('1', $this->headers['Dnt']);
        self::assertSame('Swiftly', $this->headers['X-Powered-By']);

        // Make sure empty values are ignored
        self::assertArrayNotHasKey('', $this->headers);
        self::assertNotContains('should-not-appear', $this->headers);
    }

    public function testCanSuppressErrors(): void
    {
        $current_level = error_reporting();

        Helpers::suppressErrors(function () {
            self::assertSame(0, error_reporting());
        });

        // Level is restored
        self::assertSame($current_level, error_reporting());
    }
}
