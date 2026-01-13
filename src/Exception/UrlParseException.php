<?php declare(strict_types=1);

namespace Swiftly\Http\Exception;

use RuntimeException;

use function sprintf;

/**
 * Exception used to indicate a failure occurred while parsing a URL string
 *
 * @api
 */
final class UrlParseException extends RuntimeException
{
    public static function forMalformedUrl(string $url): self
    {
        return new self(sprintf(
            'Failed to parse string "%s" as a URL because it is too badly'
            . ' malformed or in an invalid format',
            $url,
        ));
    }

    /**
     * @param non-empty-string $component
     */
    public static function forMissingComponent(
        string $url,
        string $component,
    ): self {
        return new self(sprintf(
            'Failed to fully parse URL "%s" as it lacks a %s component',
            $url,
            $component,
        ));
    }
}
