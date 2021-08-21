<?php

namespace Swiftly\Http\Server;

use Swiftly\Http\Session;
use Swiftly\Http\Cookie;
use Swiftly\Http\Cookies;
use Swiftly\Http\Headers;
use Swiftly\Http\Parameters;
use Swiftly\Http\Url;

use function in_array;

/**
 * Class used to represent HTTP requests coming into the server
 *
 * @author clvarley
 */
Class Request
{

    /**
     * Recognised HTTP methods we can respond to
     *
     * We are not currently planning to support the TRACE or CONNECT verbs.
     *
     * @var string[] ALLOWED_METHODS HTTP verbs
     */
    public const ALLOWED_METHODS = [
        'OPTIONS',
        'HEAD',
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE'
    ];

    /**
     * Current user session
     *
     * @var Session|null $session User session
     */
    public $session;

    /**
     * Request HTTP headers
     *
     * @var Headers $headers HTTP headers
     */
    public $headers;

    /**
     * Request HTTP cookies
     *
     * @var Cookies $headers HTTP cookies
     */
    public $cookies;

    /**
     * HTTP query string parameters
     *
     * @var Parameters $query Query parameters
     */
    public $query;

    /**
     * HTTP POST parameters
     *
     * @var Parameters $post POST parameters
     */
    public $post;

    /**
     * HTTP method used
     *
     * @var string $method HTTP verb
     */
    protected $method;

    /**
     * URL used to generate this request
     *
     * @var Url $url Requested URL
     */
    protected $url;

    /**
     * Request payload
     *
     * @var mixed $content Request body
     */
    protected $content;

    /**
     * Creates a new Request object from the provided values
     *
     * We do not recommend creating this object directly, instead please use the
     * RequestFactory to instantiate new instances of this class.
     *
     * @internal
     * @param string $method    HTTP verb
     * @param Url $url          Request URL
     * @param Headers $headers  HTTP headers
     * @param Cookies $cookies  Http cookies
     * @param Parameters $query Query parameters
     * @param Parameters $post  Post parameters
     */
    public function __construct( string $method, Url $url, Headers $headers, Cookies $cookies, Parameters $query, Parameters $post )
    {
        $this->method  = in_array( $method, self::ALLOWED_METHODS ) ? $method : 'GET';
        $this->url     = $url;
        $this->headers = $headers;
        $this->cookies = $cookies;
        $this->query   = $query;
        $this->post    = $post;
    }

    /**
     * Returns the HTTP method used for this request
     *
     * @return string HTTP verb
     */
    public function getMethod() : string
    {
        return $this->method;
    }

    /**
     * Returns the protocol of this request
     *
     * @return string Request protocol
     */
    public function getProtocol() : string
    {
        return $this->url->scheme;
    }

    /**
     * Returns the URL path of this request
     *
     * @return string Request path
     */
    public function getPath() : string
    {
        return $this->url->path;
    }

    /**
     * Checks if this request was made via a secure protocol
     *
     * @return bool Secure protocol
     */
    public function isSecure() : bool
    {
        return $this->url->scheme === 'https';
    }
}
