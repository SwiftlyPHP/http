<?php

namespace Swiftly\Http;

/**
 * Class used to represent and manage a single HTTP cookie
 *
 * @internal
 * @author clvarley
 */
class Cookie
{
    /** @var non-empty-string $name Cookie name */
    public string $name = '';

    /** @var non-empty-string $value Cookie value */
    public string $value = '';

    /** Expiry time as unix timestamp */
    public int $expires = 0;

    /** Allowed (sub)path */
    public string $path = '';

    /** Allowed (sub)domain */
    public string $domain = '';

    /** Requires HTTPS connection? */
    public bool $secure = true;

    /** Only readable via HTTP (and not js) */
    public bool $httponly = false;
}
