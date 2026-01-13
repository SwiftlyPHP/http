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
final class EnvironmentException extends LogicException
{
    /**
     * @param non-empty-string $variable
     */
    public static function missingServerVar(string $variable): self
    {
        return new self(sprintf(
            'The environment variable $_SERVER[\'%s\'] is required but is not'
            . ' defined, please update your server configuration to make it]'
            . ' available to PHP',
            $variable,
        ));
    }
}
