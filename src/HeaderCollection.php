<?php declare(strict_types=1);

namespace Swiftly\Http;

use function end;
use function strtolower;

/**
 * Stores HTTP header values sent with the current request
 *
 * @api
 * @psalm-type HeaderValues = non-empty-list<string>
 * @psalm-type HeaderArray = array<non-empty-string,HeaderValues>
 */
class HeaderCollection
{
    /** @var HeaderArray $values */
    private array $values = [];

    /**
     * Create a new collection containing the given headers
     *
     * @param array<non-empty-string,string> $headers HTTP header values
     */
    public function __construct(array $headers = [])
    {
        foreach ($headers as $header => $value) {
            $this->set($header, $value, false);
        }
    }

    /**
     * Set the value of a HTTP header
     *
     * @param non-empty-string $header Header name
     * @param string $value            Header value
     * @param bool $replace            Replace existing value?
     */
    public function set(
        string $header,
        string $value,
        bool $replace = true
    ): void {
        $key = $this->key($header);

        if ($replace || !isset($this->values[$key])) {
            $this->values[$key] = [];
        }

        $this->values[$key][] = $value;
    }

    /**
     * Check if a named HTTP header is present
     *
     * @psalm-assert-if-true !null $this->get($header)
     *
     * @param non-empty-string $header Header name
     * @return bool                    Header is set?
     */
    public function has(string $header): bool
    {
        $key = $this->key($header);

        return isset($this->values[$key]);
    }

    /**
     * Retrieve the value of a HTTP header
     *
     * If multiple headers with the same name have been set, the most recent
     * value will be returned. This in practice means that the last header to be
     * sent takes precedence.
     *
     * So for a request with the following headers:
     *
     * ```
     * Content-Type: text/plain;
     * X-Some-Header: foo;
     * X-Some-Header: bar;
     * ```
     *
     * Fetching the value of `X-Some-Header` will return `bar`.
     *
     * ```php
     * <?php
     *
     * $header->get('X-Some-Header') === 'bar';
     * ```
     *
     * If you need every value set for a header use the {@see self::all()}
     * method instead.
     *
     * @param non-empty-string $header Header name
     * @return null|string             Header value
     */
    public function get(string $header): ?string
    {
        $key = $this->key($header);

        if (!isset($this->values[$key])) {
            return null;
        }

        return end($this->values[$key]);
    }

    /**
     * Return all values for a given header OR all headers in the collection
     *
     * If no argument (or `null`) is provided, this method returns all HTTP
     * headers stored in the collection. However, if a header name is provided
     * either all values for that header will be returned or null if it has not
     * been set.
     *
     * @psalm-return ($header is null ? HeaderArray : ?HeaderValues)
     * @param null|non-empty-string $header  Header name
     * @return null|array<non-empty-string,non-empty-list<string>>|non-empty-list<string>
     */
    public function all(?string $header = null): ?array
    {
        if ($header === null) {
            return $this->values;
        }

        return $this->values[$this->key($header)] ?? null;
    }

    /**
     * Prepare the given subject to be used as a header key
     *
     * @param non-empty-string $key Header name
     * @return non-empty-string     Encoded header name
     */
    private function key(string $key): string
    {
        return strtolower($key);
    }
}
