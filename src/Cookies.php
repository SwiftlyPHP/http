<?php

namespace Swiftly\Http;

use Swiftly\Http\Cookie;

/**
 * Utility container for managing HTTP cookies
 *
 * @author clvarley
 */
Class Cookies
{

    /**
     * Array of HTTP cookies
     *
     * @psalm-var array<string,Cookie>
     *
     * @var Cookie[] $cookies Http cookies
     */
    protected $cookies;

    /**
     * Creates a new cookie holder from the (optionally) provided cookies
     *
     * @param Cookie[] $cookies (Optional) Http cookies
     */
    public function __construct( array $cookies = [] )
    {
        $this->cookies = $cookies;
    }

    /**
     * Sets a named cookie
     *
     * @param string $name   Cookie name
     * @param Cookie $cookie Cookie
     * @return void          N/a
     */
    public function set( string $name, Cookie $cookie ) : void
    {
        $this->cookies[$name] = $cookie;
    }

    /**
     * Gets the named cookie
     *
     * @param string $name Cookie name
     * @return Cookie|null Cookie
     */
    public function get( string $name ) : ?Cookie
    {
        return $this->cookies[$name] ?? null;
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
    ) : Cookie {
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
     * Checks to see if the named cookie exists
     *
     * @param string $name Cookie name
     * @return bool        Cookie set?
     */
    public function has( string $name ) : bool
    {
        return isset( $this->cookies[$name] );
    }

    /**
     * Gets all cookies
     *
     * @return Cookie[] Cookies
     */
    public function all() : array
    {
        return $this->cookies;
    }
}
