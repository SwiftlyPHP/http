<?php

namespace Swiftly\Http;

use Swiftly\Http\Cookie;

/**
 * Utility container for managing HTTP cookies
 *
 * @api
 */
class CookieCollection
{
    /** @var array<non-empty-string,Cookie> $cookies Stored HTTP cookies */
    protected $cookies = [];

    /**
     * Creates a new collection around the given cookies
     *
     * @param Cookie[] $cookies HTTP cookies
     */
    public function __construct(array $cookies = [])
    {
        foreach ($cookies as $cookie) {
            $this->set($cookie);
        }
    }

    /**
     * Store a cookie in the collection
     *
     * @param Cookie $cookie Cookie
     */
    public function set(Cookie $cookie): void
    {
        $this->cookies[$cookie->name] = $cookie;
    }

    /**
     * Create a new cookie within this collection
     *
     * @param non-empty-string $name Cookie name
     * @param string $value          Cookie value
     * @param int $expires           Unix timestamp
     * @param string $path           URL path
     * @param string $domain         Allowed domains
     * @param bool $secure           HTTPS only
     * @param bool $httponly         HTTP only
     * @return Cookie                Created cookie
     */
    public function add(
        string $name,
        string $value,
        int $expires = 0,
        string $path = '',
        string $domain = '',
        bool $secure = true,
        bool $httponly = false
    ): Cookie {
        $cookie = new Cookie($name, $value);
        $cookie->expires = $expires;
        $cookie->path = $path;
        $cookie->domain = $domain;
        $cookie->secure = $secure;
        $cookie->httponly = $httponly;

        return $this->cookies[$name] = $cookie;
    }

    /**
     * Return a named cookie from the collection
     *
     * @param non-empty-string $name Cookie name
     * @return null|Cookie           Cookie object
     */
    public function get(string $name): ?Cookie
    {
        return $this->cookies[$name] ?? null;
    }

    /**
     * Checks to see if the named cookie exists
     *
     * @psalm-assert-if-true Cookie $this->get($name)
     *
     * @param non-empty-string $name Cookie name
     * @return bool                  Cookie in collection?
     */
    public function has(string $name): bool
    {
        return isset($this->cookies[$name]);
    }

    /**
     * Removes the named cookie
     *
     * Because we (presumably) want to delete the cookie from the client as
     * well, we set the expiry date an hour into the past and wipe the value.
     *
     * Most spec conforming browsers treat this as an invalidation.
     *
     * @param non-empty-string $name Cookie name
     */
    public function remove(string $name): void
    {
        if (!isset($this->cookies[$name])) {
            return;
        }

        // Make sure we invalidate on the client
        $this->cookies[$name]->invalidate();
    }

    /**
     * Returns all cookies in this collection
     *
     * @return array<non-empty-string,Cookie>
     */
    public function all(): array
    {
        return $this->cookies;
    }
}
