<?php

namespace Swiftly\Http;

use Swiftly\Http\Cookie;

use function time;

/**
 * Utility container for managing HTTP cookies
 *
 * @author clvarley
 */
class Cookies
{
    /**
     * Array of HTTP cookies
     *
     * @psalm-var array<string,Cookie> $cookies
     *
     * @var Cookie[] $cookies Http cookies
     */
    protected $cookies = [];

    /**
     * Creates a new cookie holder from the (optionally) provided cookies
     *
     * @param Cookie[] $cookies (Optional) Http cookies
     */
    public function __construct(array $cookies = [])
    {
        foreach ($cookies as $cookie) {
            $this->set($cookie);
        }
    }

    /**
     * Sets a named cookie
     *
     * @param Cookie $cookie Cookie
     * @return void          N/a
     */
    public function set(Cookie $cookie): void
    {
        $this->cookies[$cookie->name] = $cookie;
    }

    /**
     * Add a new cookie to the bag (with the given attributes)
     *
     * @param string $name   Cookie name
     * @param string $value  (Optional) Cookie value
     * @param int $expires   (Optional) Unix timestamp
     * @param string $path   (Optional) URL path
     * @param string $domain (Optional) Allowed domains
     * @param bool $secure   (Optional) HTTPS only
     * @param bool $httponly (Optional) HTTP only
     * @return Cookie        Created cookie
     */
    public function add(
        string $name,
        string $value = '',
        int $expires = 0,
        string $path = '',
        string $domain = '',
        bool $secure = false,
        bool $httponly = false
    ): Cookie {
        $cookie = new Cookie;
        $cookie->name = $name;
        $cookie->value = $value;
        $cookie->expires = 0;
        $cookie->path = $path;
        $cookie->domain = $domain;
        $cookie->secure = $secure;
        $cookie->httponly = $httponly;

        return $this->cookies[$name] = $cookie;
    }

    /**
     * Gets the named cookie
     *
     * @param string $name Cookie name
     * @return Cookie|null Cookie
     */
    public function get(string $name): ?Cookie
    {
        return $this->cookies[$name] ?? null;
    }

    /**
     * Checks to see if the named cookie exists
     *
     * @param string $name Cookie name
     * @return bool        Cookie set?
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
     * @param string $name Cookie name
     * @return void        N/a
     */
    public function remove(string $name): void
    {
        if (!isset($this->cookies[$name])) {
            return;
        }

        // We actually want to invalidate it on the client
        $this->cookies[$name]->expires = (time() - 3600);
        $this->cookies[$name]->value = '';

        return;
    }

    /**
     * Gets all cookies
     *
     * @psalm-return array<string,Cookie>
     *
     * @return Cookie[] Cookies
     */
    public function all(): array
    {
        return $this->cookies;
    }
}
