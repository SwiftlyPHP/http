<?php declare(strict_types=1);

namespace Swiftly\Http\Exception;

/**
 * Exception used to indicate a failure occured when reading from a session
 *
 * @see \Swiftly\Http\Exception\SessionException
 * @see \Swiftly\Http\SessionHandler::has()
 * @see \Swiftly\Http\SessionHandler::read()
 *
 * @api
 */
final class SessionReadException extends SessionException
{
    /**
     * @param non-empty-string $reason Read error reason
     */
    public function __construct(string $reason)
    {
        parent::__construct('read data from', $reason);
    }
}
