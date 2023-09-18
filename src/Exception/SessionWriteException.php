<?php

namespace Swiftly\Http\Exception;

use Swiftly\Http\Exception\SessionException;

/**
 * Exception used to indicate a failure occured when writing to a session
 *
 * @see \Swiftly\Http\Exception\SessionException
 * @see \Swiftly\Http\SessionHandler::write()
 * @see \Swiftly\Http\SessionHandler::persist()
 *
 * @api
 */
final class SessionWriteException extends SessionException
{
    /**
     * @param non-empty-string $reason Write error reason
     */
    public function __construct(string $reason)
    {
        parent::__construct('write data to', $reason);
    }
}
