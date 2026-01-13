<?php declare(strict_types=1);

namespace Swiftly\Http\Exception;

use LogicException;
use Swiftly\Http\Helpers;

use function sprintf;

final class HeaderException extends LogicException
{
    public static function forRequestOnlyHeader(string $headerName): self
    {
        return new self(sprintf(
            'Could not apply the "%s" header as it is a request only header and'
            . ' is not meant to be used for HTTP responses',
            Helpers::pascalCase($headerName),
        ));
    }
}
