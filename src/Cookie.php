<?php

namespace Swiftly\Http;

/**
 * Class used to represent and manage a single HTTP cookie
 *
 * @internal
 * @author clvarley
 */
Class Cookie
{

    /**
     * The name of this cookie
     *
     * @var string $name Cookie name
     */
    public $name = '';

    /**
     * The value of this cookie
     *
     * @var string $value Cookie value
     */
    public $value = '';

    /**
     * Expiry date of this cookie
     *
     * @var int $expires Unix timestamp
     */
    public $expires = 0;

    /**
     * Allowed URL path
     *
     * @var string $path URL path
     */
    public $path = '';

    /**
     * Allowed (sub)domains
     *
     * @var string $domain Allowed domains
     */
    public $domain = '';

    /**
     * Only transmitt over HTTPS
     *
     * @var bool $secure HTTPS only
     */
    public $secure = false;

    /**
     * Only readable via HTTP (and not js)
     *
     * @var bool $httponly HTTP only
     */
    public $httponly = false;

}
