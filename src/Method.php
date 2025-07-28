<?php declare(strict_types=1);

namespace Swiftly\Http;

use function in_array;

/**
 * Provides helpers for dealing with HTTP methods.
 *
 * @api
 *
 * @psalm-immutable
 * @psalm-type SafeMethod = "GET"|"HEAD"|"OPTIONS"|"TRACE"
 * @psalm-type UnsafeMethod = "POST"|"PUT"|"DELETE"|"CONNECT"
 * @psalm-type HttpMethod = SafeMethod|UnsafeMethod
 */
abstract class Method
{
    public const GET = 'GET';
    public const HEAD = 'HEAD';
    public const POST = 'POST';
    public const PUT = 'PUT';
    public const DELETE = 'DELETE';
    public const CONNECT = 'CONNECT';
    public const OPTIONS = 'OPTIONS';
    public const TRACE = 'TRACE';

    private const KNOWN_METHODS = [
        self::GET,
        self::HEAD,
        self::POST,
        self::PUT,
        self::DELETE,
        self::CONNECT,
        self::OPTIONS,
        self::TRACE
    ];

    private const SAFE_METHODS = [
        self::GET,
        self::HEAD,
        self::OPTIONS,
        self::TRACE
    ];

    private const CACHEABLE_METHODS = [
        self::GET,
        self::HEAD
    ];

    /**
     * Determine if the given string is a known HTTP method.
     *
     * @link https://www.rfc-editor.org/rfc/rfc9110.html#name-methods
     *
     * @psalm-pure
     * @psalm-assert-if-true HttpMethod $method
     *
     * @param non-empty-string $method
     */
    final public static function isKnownMethod(string $method): bool
    {
        return in_array($method, self::KNOWN_METHODS, true);
    }

    /**
     * Determine if the given string is a known, safe HTTP method.
     *
     * @link https://www.rfc-editor.org/rfc/rfc9110.html#name-safe-methods
     *
     * @psalm-pure
     * @psalm-assert-if-true "GET"|"HEAD"|"OPTIONS"|"TRACE" $subject
     *
     * @param non-empty-string $method
     */
    final public static function isSafeMethod(string $method): bool
    {
        return in_array($method, self::SAFE_METHODS, true);
    }

    /**
     * Determine if the given string is a known, cacheable HTTP method.
     *
     * @link https://www.rfc-editor.org/rfc/rfc9110.html#name-methods-and-caching
     *
     * @psalm-pure
     * @psalm-assert-if-true "GET"|"HEAD" $subject
     *
     * @param non-empty-string $method
     */
    final public static function isCacheableMethod(string $method): bool
    {
        return in_array($method, self::CACHEABLE_METHODS, true);
    }
}
