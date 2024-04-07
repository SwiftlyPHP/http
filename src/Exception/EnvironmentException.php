<?php declare(strict_types=1);

namespace Swiftly\Http\Exception;

use LogicException;

use function sprintf;

/**
 * Exception used to indicate a problem with the environment PHP is running on
 *
 * @see \Swiftly\Http\Request\Request::fromGlobals()
 * @see \Swiftly\Http\Url::fromGlobals()
 *
 * @api
 */
class EnvironmentException extends LogicException
{
    /**
     * @param non-empty-string $reason Failure reason
     */
    public function __construct(string $reason)
    {
        parent::__construct(
            sprintf(
                'Failed due to a problem with the environment: %s',
                $reason
            )
        );
    }
}
