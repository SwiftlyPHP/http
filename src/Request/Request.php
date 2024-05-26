<?php declare(strict_types=1);

namespace Swiftly\Http\Request;

use Swiftly\Http\HeaderCollection;
use Swiftly\Http\CookieCollection;
use Swiftly\Http\SessionHandler;
use Swiftly\Http\SessionStorageInterface;
use Swiftly\Http\ParameterCollection;
use Swiftly\Http\Url;
use Swiftly\Http\Method;
use Swiftly\Http\Exception\SessionException;
use Swiftly\Http\Exception\UrlParseException;
use Swiftly\Http\Exception\EnvironmentException;
use Swiftly\Http\Helpers;

/**
 * Class used to represent HTTP requests coming into the server
 *
 * @api
 * @php:8.1 Swap to readonly properties
 * @psalm-import-type ParameterArray from ParameterCollection
 * @psalm-import-type HttpMethod from Method
 */
class Request
{
    /**
     * Client provided HTTP headers
     *
     * @readonly
     */
    public HeaderCollection $headers;

    /**
     * Client provided HTTP cookies
     *
     * @readonly
     */
    public CookieCollection $cookies;

    /** Current user session */
    protected ?SessionHandler $session;

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
        $this->method  = $method;
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
     * @psalm-mutation-free
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
     * @psalm-mutation-free
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
     * @psalm-mutation-free
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
     * @psalm-mutation-free
     *
     * @return bool Secure protocol
     */
    public function isSecure(): bool
    {
        return $this->url->protocol === 'https';
    }

    /**
     * Checks if this request was made using a known HTTP method
     *
     * @psalm-mutation-free
     * @psalm-assert-if-true HttpMethod $this->method
     * @psalm-assert-if-true HttpMethod $this->getMethod()
     *
     * @return bool Known HTTP method
     */
    public function isKnownMethod(): bool
    {
        return Method::isKnownMethod($this->method);
    }

    /**
     * Checks if this request was made using a safe HTTP method
     *
     * @psalm-mutation-free
     * @psalm-assert-if-true "GET"|"HEAD"|"OPTIONS"|"TRACE" $this->method
     * @psalm-assert-if-true "GET"|"HEAD"|"OPTIONS"|"TRACE" $this->getMethod()
     *
     * @return bool Safe HTTP method
     */
    public function isSafeMethod(): bool
    {
        return Method::isSafeMethod($this->method);
    }

    /**
     * Check if cached responses are allowed to be returned for this request
     *
     * @psalm-mutation-free
     * @psalm-assert-if-true "GET"|"HEAD" $this->method
     * @psalm-assert-if-true "GET"|"HEAD" $this->getMethod()
     *
     * @return bool Allows cached responses
     */
    public function allowsCachedResponses(): bool
    {
        return Method::isCacheableMethod($this->method);
    }

    /**
     * Checks to see is a user session is attached to this request
     *
     * @psalm-mutation-free
     * @psalm-assert-if-true SessionHandler $this->session
     * @psalm-assert-if-true SessionHandler $this->getSession()
     */
    public function hasSession(): bool
    {
        return $this->session !== null;
    }

    /**
     * Get the current session handler
     *
     * @throws SessionException
     *          If no session has been attached to this request
     *
     * @psalm-mutation-free
     * @psalm-assert SessionHandler $this->session
     * @return SessionHandler Session handler
     */
    public function getSession(): SessionHandler
    {
        if ($this->session === null) {
            throw new SessionException(
                'fetch',
                'request has no attached session'
            );
        }

        return $this->session;
    }

    /**
     * Attach a session to this request
     *
     * @throws SessionException
     *          If a session has already been attached to this request
     *
     * @php:8.0 Use union type hint
     * @psalm-assert null $this->session
     * @param SessionHandler|SessionStorageInterface $session Session handler
     * @return SessionHandler                                 Attached session
     */
    public function setSession($session): SessionHandler
    {
        if ($this->session !== null) {
            throw new SessionException(
                'attach',
                'request already has attached session'
            );
        }

        if ($session instanceof SessionStorageInterface) {
            $session = new SessionHandler($session);
        }

        $session->attach($this);

        return $this->session = $session;
    }

    /**
     * Create a new HTTP request with the provided details
     *
     * @throws UrlParseException
     *          If the value given for `$url` cannot be parsed
     *
     * @psalm-mutation-free
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
     * @throws EnvironmentException
     *          If PHP global `REQUEST_METHOD` value is undefined
     *
     * @psalm-mutation-free
     * @return self Request instance
     */
    public static function fromGlobals(): self
    {
        if (!isset($_SERVER['REQUEST_METHOD'])) {
            throw new EnvironmentException(
                "\$_SERVER['REQUEST_METHOD'] is undefined"
            );
        }

        return new self(
            $_SERVER['REQUEST_METHOD'],
            Url::fromGlobals(),
            new HeaderCollection(Helpers::getHeaders()),
            CookieCollection::fromGlobals(),
            new ParameterCollection($_GET),
            new ParameterCollection($_POST)
        );
    }
}
