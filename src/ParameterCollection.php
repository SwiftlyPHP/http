<?php declare(strict_types=1);

namespace Swiftly\Http;

use function explode;
use function is_numeric;

/**
 * Stores HTTP parameters passed via query string or POST payload.
 *
 * @api
 * @upgrade:php8.1 Mark properties readonly
 *
 * @psalm-immutable
 * @psalm-type ParameterValue = string|array<string, string|array>
 * @psalm-type ParameterArray = array<string, ParameterValue>
 */
class ParameterCollection
{
    /** @var ParameterArray */
    private array $values;

    /**
     * Create a new collection around the given parameters.
     *
     * @param ParameterArray $values Parameter values.
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * Check to see if the given parameter is set.
     *
     * @psalm-assert-if-true !null $this->get($key)
     */
    public function has(int|string $key): bool
    {
        return isset($this->values[$key]);
    }

    /**
     * Returns the value of a named parameter.
     *
     * @return ParameterValue|null
     */
    public function get(int|string $key): array|string|null
    {
        return $this->values[$key] ?? null;
    }

    /**
     * Return the value of a nested parameter.
     *
     * PHP allows nested query/post data to be sent in the form
     * `key[subkey]=value`, which is normally surfaced inside the `$_GET` and
     * `$_POST` globals as nested arrays.
     *
     * ```php
     * <?php // http://localhost?data[name]=value
     *
     * $_GET['data']['name'] === 'value';
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
     * @return ParameterValue|null
     */
    public function getNested(string $key, string $delimiter = '.'): array|string|null
    {
        $values = $this->values;

        foreach (explode($delimiter, $key) as $subkey) {
            if (!isset($values[$subkey])) {
                return null;
            }

            $values = $values[$subkey];
        }

        return $values;
    }

    /**
     * Returns the value of a named parameter as a integer.
     */
    public function getInt(int|string $key): ?int
    {
        if (null === ($value = $this->get($key))) {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    /**
     * Returns the value of a named parameter as a float.
     */
    public function getFloat(int|string $key): ?float
    {
        if (null === ($value = $this->get($key))) {
            return null;
        }

        return is_numeric($value) ? (float) $value : null;
    }

    /**
     * Returns the value of a named parameter as a string.
     */
    public function getString(int|string $key): ?string
    {
        $value = $this->get($key);

        if (null === $value || is_array($value)) {
            return null;
        }

        return (string) $value;
    }
}
