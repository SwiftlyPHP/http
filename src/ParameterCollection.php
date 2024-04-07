<?php declare(strict_types=1);

namespace Swiftly\Http;

use function explode;
use function is_numeric;

/**
 * Stores HTTP parameters passed via query string or POST payload
 *
 * @api
 * @php:8.0 Add union type hints
 * @php:8.0 Add readonly attribute
 * @psalm-immutable
 * @psalm-type ParameterKey = int|non-empty-string
 * @psalm-type ParameterValue = string|array<ParameterKey,string|array>
 * @psalm-type ParameterArray = array<ParameterKey,ParameterValue>
 */
class ParameterCollection
{
    /** @var ParameterArray $values */
    private array $values;

    /**
     * Create a new collection around the given parameters
     *
     * @param ParameterArray $values Parameter values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * Check to see if the given parameter is set
     *
     * @psalm-assert-if-true !null $this->get($key)
     *
     * @param ParameterKey $key Parameter key
     * @return bool             Parameter is set?
     */
    public function has($key): bool
    {
        return isset($this->values[$key]);
    }

    /**
     * Returns the value of a named parameter or null if not set
     *
     * @param ParameterKey $key Parameter key
     * @return ?ParameterValue  Parameter value or null
     */
    public function get($key) // :string|array|null
    {
        return $this->values[$key] ?? null;
    }

    /**
     * Return the value of a nested parameter
     *
     * PHP allows nested query/post data to be sent in the form
     * `key[subkey]=value`, which is normally surfaced inside the `$_GET` and
     * `$_POST` globals as nested arrays.
     * 
     * ```php
     * <?php // http://localhost?data[name]=value
     * 
     * $_GET['data]['name] === 'value';
     * ```
     * 
     * This method allows you to access these nested values by using a delimited
     * key.
     * 
     * ```php
     * <?php // http://localhost?data[name]=value
     * 
     * $parameters->getNested('data.name') === 'value';
     * ```
     *
     * @param non-empty-string $key       Parameter key
     * @param non-empty-string $delimiter Key delimiter
     * @return null|string|array          Nested value
     */
    public function getNested(string $key, string $delimiter = ".") // :null|string|array
    {
        $values = $this->values;

        foreach (explode($delimiter, $key) as $subkey) {
            if (!isset($values[$subkey])) return null;

            $values = $values[$subkey];
        }

        return $values;
    }

    /**
     * Returns the value of a named parameter as a integer
     *
     * @param ParameterKey $key Parameter key
     * @return ?int             Integer value
     */
    public function getInt($key): ?int
    {
        if (($value = $this->get($key)) === null) {
            return null;
        }

        return (is_numeric($value) ? (int)$value : null);
    }

    /**
     * Returns the value of a named parameter as a float
     *
     * @param ParameterKey $key Parameter key
     * @return ?float           Floating point value
     */
    public function getFloat($key): ?float
    {
        if (($value = $this->get($key)) === null) {
            return null;
        }

        return (is_numeric($value) ? (float)$value : null);
    }
}
