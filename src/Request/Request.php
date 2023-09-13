<?php

namespace Swiftly\Http\Request;

use Swiftly\Http\Session;
use Swiftly\Http\HeaderCollection;
use Swiftly\Http\CookieCollection;
use Swiftly\Http\ParameterCollection;
use Swiftly\Http\Url;
use Swiftly\Http\Exception\RequestCreationException;
use Swiftly\Http\Helpers;

use function in_array;

/**
 * Class used to represent HTTP requests coming into the server
 *
 * @api
 * @php:8.1 Swap to readonly properties
 * @psalm-import-type ParameterArray from ParameterCollection
 */
class Request
{
    /** Recognised HTTP verbs */
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
     * Request HTTP headers
     *
     * @readonly
     */
    public HeaderCollection $headers;

    /**
     * Request HTTP cookies
     *
     * @readonly
     */
    public CookieCollection $cookies;

    /**
     * Current user session
     *
     * @var null|Session $session User session
     */
    public ?Session $session;

    /**
     * HTTP query string parameters
     *
     * @readonly
     */
    public ParameterCollection $query;

    /**
     * HTTP POST parameters
     *
     * @readonly
     */
    public ParameterCollection $post;

    /**
     * HTTP method used
     *
     * @readonly
     * @var non-empty-string $method HTTP verb
     */
    protected string $method;

    /**
     * URL used to generate this request
     *
     * @readonly
     */
    protected URL $url;

    /** Request payload */
    protected ?string $content;

    /**
     * Creates a new Request object from the provided values
     *
     * The API for the constructor is not part of the backwards compatibility
     * guarantee, please use the {@see self::create()} or
     * {@see self::fromGlobals()} methods instead.
     *
     * @internal
     * @param non-empty-string $method   HTTP verb
     * @param Url $url                   Request URL
     * @param HeaderCollection $headers  HTTP headers
     * @param CookieCollection $cookies  Http cookies
     * @param ParameterCollection $query Query parameters
     * @param ParameterCollection $post  Post parameters
     */
    public function __construct(
        string $method,
        Url $url,
        HeaderCollection $headers,
        CookieCollection $cookies,
        ParameterCollection $query,
        ParameterCollection $post
    ) {
        $this->method  = in_array($method, self::ALLOWED_METHODS) ? $method : 'GET';
        $this->url     = $url;
        $this->headers = $headers;
        $this->cookies = $cookies;
        $this->query   = $query;
        $this->post    = $post;
        $this->session = null;
        $this->content = null;
    }

    /**
     * Returns the HTTP method used for this request
     *
     * @return non-empty-string HTTP verb
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Returns the protocol of this request
     *
     * @return non-empty-string Request protocol
     */
    public function getProtocol(): string
    {
        return $this->url->protocol;
    }

    /**
     * Returns the URL path of this request
     *
     * @return non-empty-string Request path
     */
    public function getPath(): string
    {
        return $this->url->path;
    }

    /**
     * Checks if this request was made via a secure protocol
     *
     * @return bool Secure protocol
     */
    public function isSecure(): bool
    {
        return $this->url->protocol === 'https';
    }

    /**
     * Create a new HTTP request with the provided details
     *
     * @param non-empty-string $method                HTTP verb
     * @param non-empty-string $url                   Requested URL
     * @param array<non-empty-string,string> $headers HTTP header values
     * @param ParameterArray $query                   HTTP query values
     * @param ParameterArray $post                    HTTP POST values
     * @return self                                   Request instance
     */
    public static function create(
        string $method,
        string $url,
        array $headers = [],
        array $query = [],
        array $post = []
    ): self {
        return new self(
            $method,
            Url::fromString($url),
            new HeaderCollection($headers),
            new CookieCollection(),
            new ParameterCollection($query),
            new ParameterCollection($post)
        );
    }

    /**
     * Create a new HTTP request using the current PHP globals
     *
     * @throws RequestCreationException
     *          If PHP global `REQUEST_METHOD` value is undefined
     *
     * @return self Request instance
     */
    public static function fromGlobals(): self
    {
        if (!isset($_SERVER['REQUEST_METHOD'])) {
            throw new RequestCreationException(
                "required \$_SERVER value missing"
            );
        }

        return new self(
            $_SERVER['REQUEST_METHOD'],
            Url::fromGlobals(),
            new HeaderCollection(Helpers::getHeaders()),
            new CookieCollection(),
            new ParameterCollection($_GET),
            new ParameterCollection($_POST)
        );
    }
}
