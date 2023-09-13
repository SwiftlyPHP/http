<?php

namespace Swiftly\Http;

use Stringable;
use Swiftly\Http\Exception\UrlParseException;
use Swiftly\Http\Exception\UrlCreationException;

use function parse_url;

/**
 * Utility class for representing URLs
 *
 * @internal
 * @psalm-immutable
 */
final class Url implements Stringable
{
    /** @var non-empty-string $protocol The protocol being used */
    public string $protocol;

    /** @var non-empty-string $domain The requested domain */
    public string $domain;

    /** @var non-empty-string $path Path component */
    public string $path;

    /** Optional query parameters */
    public string $query;

    /** Resource fragment */
    public string $fragment;

    /**
     * Create a new URL with the given components
     *
     * @param non-empty-string $protocol URL protocol/scheme
     * @param non-empty-string $domain   Target domain
     * @param non-empty-string $path     Requested path
     * @param string $query              Query component
     * @param string $fragment           Fragment identifier
     */
    public function __construct(
        string $protocol,
        string $domain,
        string $path,
        string $query = '',
        string $fragment = ''
    ) {
        $this->protocol = $protocol;
        $this->domain = $domain;
        $this->path = $path;
        $this->query = $query;
        $this->fragment = $fragment;
    }

    /**
     * Returns the string representation of this URL
     *
     * @return non-empty-string URL string
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
     * Attempt to parse URL information from the given string
     *
     * @throws UrlParseException If the given string cannot be parsed
     *
     * @param string $url URL like string
     * @return Url        URL object
     */
    public static function fromString(string $url): Url
    {
        if (($parts = parse_url($url)) === false) {
            throw new UrlParseException($url);
        }

        // Check required components
        if (empty($parts['host'])) {
            throw new UrlParseException($url, 'lacks hostname');
        }

        if (empty($parts['path'])) {
            throw new UrlParseException($url, 'lacks path component');
        }

        // Never assume HTTPS (has to be explicitly set)
        return new self(
            !empty($parts['scheme']) ? $parts['scheme'] : 'http',
            $parts['host'],
            $parts['path'],
            $parts['query'] ?? '',
            $parts['fragment'] ?? ''
        );
    }

    /**
     * Creates a Url object from the global $_SERVER variable
     *
     * @throws UrlCreationException
     *          If PHP global `HTTP_HOST` or `REQUEST_URI` values are undefined
     *
     * @return self Url object
     */
    public static function fromGlobals(): self
    {
        if (empty($_SERVER['HTTP_HOST']) || empty($_SERVER['REQUEST_URI'])) {
            throw new UrlCreationException("required \$_SERVER values missing");
        }

        // Connection protocol
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $scheme = 'https';
        } else if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
            && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
        ) {
            $scheme = 'https';
        } else {
            $scheme = 'http';
        }

        return self::fromString(
            "$scheme://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"
        );
    }
}
