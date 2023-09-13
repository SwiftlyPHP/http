<?php

namespace Swiftly\Http;

use function is_string;
use function strpos;
use function substr;
use function str_replace;
use function strtolower;
use function ucwords;

/**
 * Utility class containing library specific helper functions
 *
 * @internal
 */
abstract class Helpers
{
    /**
     * Return an array containing all headers sent with the current request
     *
     * Not all installations of PHP have access to the `getallheaders` function
     * and Psalm does not correctly type its return value. This helper is
     * designed to:
     * * Provide a way of fetching headers that works across all installations
     * * Ensure the return type is one we're expecting
     *
     * @return array<non-empty-string,string> Header values
     */
    final public static function getHeaders(): array
    {
        $headers = [];

        foreach ($_SERVER as $name => $value) {
            if (!is_string($value) || strpos($name, 'HTTP_') !== 0) {
                continue;
            }

            $name = substr($name, 5);
            $name = str_replace('_', '-', $name);
            
            if (empty($name)) {
                continue;
            }
            
            $headers[self::pascalCase($name)] = $value;
        }

        return $headers;
    }

    /**
     * Convert the subject string to `PascalCase`
     *
     * @psalm-return ($subject is non-empty-string ? non-empty-string : string)
     *
     * @param string $subject   Subject string
     * @param string $delimiter Word delimiters
     * @return string           Pascal case'd string
     */
    final public static function pascalCase(
        string $subject,
        string $delimiters = " \t\r\n\f\v-_"
    ): string {
        return ucwords(strtolower($subject), $delimiters);
    }
}
