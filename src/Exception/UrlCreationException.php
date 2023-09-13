<?php

namespace Swiftly\Http\Exception;

use LogicException;

use function sprintf;

/**
 * Exception to indicate a failure when creating a new `Url` instance
 *
 * @see \Swiftly\Http\Url::fromGlobals()
 *
 * @api
 */
final class UrlCreationException extends LogicException
{
    /**
     * @param non-empty-string $reason Failure reason
     */
    public function __construct(string $reason)
    {
        parent::__construct(
            sprintf(
                "Failed to create new URL instance because %s",
                $reason
            )
        );
    }
}
