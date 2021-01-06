<?php

namespace Swiftly\Http;

use Swiftly\Http\Cookie;

/**
 * Utility container for managing HTTP cookies
 *
 * @author C Varley <clvarley>
 */
Class Cookies
{

    /**
     * Array of HTTP cookies
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
