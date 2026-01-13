<?php declare(strict_types=1);

namespace Swiftly\Http\Exception;

use LogicException;

use function sprintf;

/**
 * Exception used to indicate a failure occured when interacting with a session
 *
 * @api Includes guarantee that this class will never be marked *final*.
 */
class SessionException extends LogicException
{
    final public static function missingFromRequest(): self
    {
        return new self(
            'Could not get the session information for this request as one has'
            . ' yet to be assigned to it',
        );
    }

    final public static function alreadyAssignedToRequest(): self
    {
        return new self(
            'Could not assign a new session to this request as it already has'
            . ' an open session assigned to it',
        );
    }

    final public static function invalidOpenCall(): self
    {
        return new self(
            'Failed to open the user session as it is already open,'
            . ' SessionHandler::open() should only be called once',
        );
    }

    final public static function invalidCloseCall(): self
    {
        return new self(
            'Failed to close the user session as it is already closed,'
            . ' SessionHandler::close() should only be called once',
        );
    }

    final public static function cannotReopenWhenClosed(): self
    {
        return new self(
            'Failed to re-open the user session; it has already been closed and'
            . ' persisted so is now locked from further changes',
        );
    }

    final public static function cannotCloseWhenUnopened(): self
    {
        return new self(
            'Failed to close the user session as it was never opened, calls to'
            . ' SessionHandler::close() are only needed for open sessions',
        );
    }

    final public static function errorOnOpen(string $error): self
    {
        return new self(sprintf(
            'Failed to open the user session due to unexpected error: %s',
            $error,
        ));
    }

    final public static function errorOnClose(string $error): self
    {
        return new self(sprintf(
            'Failed to close the user session due to unexpected error: %s',
            $error,
        ));
    }

    final public static function errorOnRead(string $error): self
    {
        return new self(sprintf(
            'Failed to read data from the user session due to an unexpected'
            . ' error: %s',
            $error,
        ));
    }

    final public static function errorOnWrite(string $error): self
    {
        return new self(sprintf(
            'Failed to write data to the user session due to an unexpected'
            . ' error: %s',
            $error,
        ));
    }

    final public static function errorOnDelete(string $error): self
    {
        return new self(sprintf(
            'Failed to remove data from the user session due to an unexpected'
            . ' error: %s',
            $error,
        ));
    }
}
