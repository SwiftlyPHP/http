<?php declare(strict_types=1);

namespace Swiftly\Http\Exception;

use LogicException;

use function sprintf;

/**
 * Exception used to indicate a failure occured when interacting with a session
 *
 * @see \Swiftly\Http\Exception\SessionReadException
 * @see \Swiftly\Http\Exception\SessionWriteException
 *
 * @api
 */
class SessionException extends LogicException
{
    /**
     * @param non-empty-string $context Error context
     * @param non-empty-string $reason  Error reason
     */
    public function __construct(string $context, string $reason)
    {
        parent::__construct(
            sprintf(
                "Failure to %s session: %s",
                $context,
                $reason
            )
        );
    }
}
