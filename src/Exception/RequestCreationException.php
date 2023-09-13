<?php

namespace Swiftly\Http\Exception;

use LogicException;

use function sprintf;

/**
 * Exception to indicate a failure when creating a new `Request` instance
 * 
 * @see \Swiftly\Http\Request\Request::fromGlobals()
 * @api
 */
final class RequestCreationException extends LogicException
{
    /**
     * @param non-empty-string $reason Failure reason
     */
    public function __construct(string $reason)
    {
        parent::__construct(
            sprintf(
                "Failed tp create a new Request instance because %s",
                $reason
            )
        );
    }
}


