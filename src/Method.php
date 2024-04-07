<?php declare(strict_types=1);

namespace Swiftly\Http;

use function in_array;

/**
 * Provides helpers for dealing with HTTP methods
 *
 * @api
 * @php:8.1 Possibly swap to using an enum
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
     * Determine if the given string is a known HTTP method
     *
     * @see https://www.rfc-editor.org/rfc/rfc9110.html#name-methods
     *
     * @psalm-pure
     * @psalm-assert-if-true HttpMethod $subject
     *
     * @param non-empty-string $subject Subject string
     * @return bool                     Is known HTTP method
     */
    final public static function isKnownMethod(string $subject): bool
    {
        return in_array($subject, self::KNOWN_METHODS, true);
    }

    /**
     * Determine if the given string is a known, safe HTTP method
     *
     * @see https://www.rfc-editor.org/rfc/rfc9110.html#name-safe-methods
     *
     * @psalm-pure
     * @psalm-assert-if-true "GET"|"HEAD"|"OPTIONS"|"TRACE" $subject
     *
     * @param non-empty-string $subject Subject string
     * @return bool                     Is safe HTTP method
     */
    final public static function isSafeMethod(string $subject): bool
    {
        return in_array($subject, self::SAFE_METHODS, true);
    }

    /**
     * Determine if the given string is a known, cacheable HTTP method
     *
     * @see https://www.rfc-editor.org/rfc/rfc9110.html#name-methods-and-caching
     *
     * @psalm-pure
     * @psalm-assert-if-true "GET"|"HEAD" $subject
     *
     * @param non-empty-string $subject Subject string
     * @return bool                     Is cacheable method
     */
    final public static function isCacheableMethod(string $subject): bool
    {
        return in_array($subject, self::CACHEABLE_METHODS, true);
    }
}
