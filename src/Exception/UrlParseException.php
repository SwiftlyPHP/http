<?php

namespace Swiftly\Http\Exception;

use RuntimeException;

use function sprintf;

/**
 * Exception to indicate a failure occurred while parsing a URL string
 *
 * @api
 */
final class UrlParseException extends RuntimeException
{
    /**
     * @param string $subject          Subject string
     * @param non-empty-string $reason Failure reason
     */
    public function __construct(
        string $subject,
        string $reason = 'is an invalid format'
    ) {
        parent::__construct(
            sprintf(
                "Failed to parse string '%s' as a URL because it %s",
                $subject,
                $reason
            )
        );
    }
}
