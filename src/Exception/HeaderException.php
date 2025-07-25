<?php declare(strict_types=1);

namespace Swiftly\Http\Exception;

use Swiftly\Http\Helpers;
use LogicException;

use function sprintf;

final class HeaderException extends LogicException
{
    private const IS_MALFORMED = 'is malformed';

    /**
     * Happens when a header value is available but is invalid in some way.
     *
     * @return HeaderException
     * @param string $headerName
     * @param string $reason
     */
    public static function forParseError(
        string $headerName,
        string $reason = self::IS_MALFORMED,
    ): self {
        return new self(sprintf(
            'Failed to parse value of "%s" header as the given string %s.',
            $headerName,
            $reason,
        ));
    }

    /**
     * Happens when trying to apply a request only header to a HTTP response.
     *
     * @return HeaderException
     * @param string $headerName
     */
    public static function forRequestOnlyHeader(string $headerName): self
    {
        return new self(sprintf(
            'Could not apply the "%s" header as it is not meant to be used'
            . ' with HTTP responses.',
            Helpers::pascalCase($headerName),
        ));
    }
}
