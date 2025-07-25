<?php declare(strict_types=1);

namespace Swiftly\Http;

use Stringable;
use Swiftly\Http\Exception\HeaderException;
use Swiftly\Http\Response\Response;

/**
 * @upgrade:php8.1 Use interface constant instead of static ::name()
 * @upgrade:php8.3 Type NAME constant as string
 */
interface HeaderLineInterface extends Stringable
{
    /**
     * Return the header name.
     *
     * @return non-empty-string
     */
    public static function name(): string;

    /**
     * Parse a single HTTP header line.
     *
     * Will throw if the header value is malformed.
     *
     * @throws HeaderException
     */
    public static function fromValue(string $value): static;

    /**
     * Apply the values of this header to a HTTP response.
     *
     * @throws HeaderException
     */
    public function applyTo(Response $response): void;
}
