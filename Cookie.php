<?php

namespace Swiftly\Http;

/**
 * Class used to represent and manage a single HTTP cookie
 *
 * @author clvarley
 */
Class Cookie
{

    /**
     * The name of this cookie
     *
     * @var string $name Cookie name
     */
    protected $name;

    /**
     * The value of this cookie
     *
     * @var string $value Cookie value
     */
    protected $value;

    /**
     * Expiry date of this cookie
     *
     * @var int $expires Unix timestamp
     */
    protected $expires;

    /**
     * Allowed URL path
     *
     * @var string $path URL path
     */
    protected $path;

    /**
     * Allowed (sub)domains
     *
     * @var string $domain Allowed domains
     */
    protected $domain;

    /**
     * Only transmitt over HTTPS
     *
     * @var bool $secure HTTPS only
     */
    protected $secure;

    /**
     * Only readable via HTTP (and not js)
     *
     * @var bool $httponly HTTP only
     */
    protected $httponly;

}
