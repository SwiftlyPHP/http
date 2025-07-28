<?php declare(strict_types=1);

namespace Swiftly\Http;

use Stringable;
use Swiftly\Http\Exception\UrlParseException;
use Swiftly\Http\Exception\EnvironmentException;

use function parse_url;

/**
 * Utility class for representing URLs.
 *
 * @internal
 * @psalm-immutable
 *
 * @upgrade:php8.1 Add readonly attribute
 */
final class Url implements Stringable
{
    /** @var non-empty-string */
    public string $protocol;

    /** @var non-empty-string */
    public string $domain;

    /** @var non-empty-string */
    public string $path;

    public string $query;

    public string $fragment;

    /**
     * Create a new URL with the given components.
     *
     * @param non-empty-string $protocol
     * @param non-empty-string $domain
     * @param non-empty-string $path
     */
    public function __construct(
        string $protocol,
        string $domain,
        string $path,
        string $query = '',
        string $fragment = '',
    ) {
        $this->protocol = $protocol;
        $this->domain = $domain;
        $this->path = $path;
        $this->query = $query;
        $this->fragment = $fragment;
    }

    /**
     * Returns the string representation of this URL.
     *
     * @return non-empty-string
     */
    public function __toString(): string
    {
        $url = "{$this->protocol}://{$this->domain}{$this->path}";

        if (!empty($this->query)) {
            $url .= "?{$this->query}";
        }

        if (!empty($this->fragment)) {
            $url .= "#{$this->fragment}";
        }

        return $url;
    }

    /**
     * Attempt to parse URL information from the given string.
     *
     * @throws UrlParseException If the given string cannot be parsed.
     *
     * @psalm-mutation-free
     */
    public static function fromString(string $url): Url
    {
        if (($parts = parse_url($url)) === false) {
            throw new UrlParseException($url);
        }

        if (empty($parts['host'])) {
            throw new UrlParseException($url, 'lacks hostname');
        }

        if (empty($parts['path'])) {
            throw new UrlParseException($url, 'lacks path component');
        }

        // Assume insecure, HTTPS has to be explicitly set
        return new self(
            !empty($parts['scheme']) ? $parts['scheme'] : 'http',
            $parts['host'],
            $parts['path'],
            $parts['query'] ?? '',
            $parts['fragment'] ?? '',
        );
    }

    /**
     * Creates a URL object from the current PHP globals.
     *
     * @throws EnvironmentException
     *          If PHP global `HTTP_HOST` or `REQUEST_URI` values are undefined.
     *
     * @psalm-mutation-free
     */
    public static function fromGlobals(): self
    {
        if (empty($_SERVER['HTTP_HOST']) || empty($_SERVER['REQUEST_URI'])) {
            throw new EnvironmentException('required $_SERVER values missing');
        }

        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $scheme = 'https';
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
            && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
        ) {
            $scheme = 'https';
        } else {
            $scheme = 'http';
        }

        return self::fromString(
            "$scheme://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}",
        );
    }
}
