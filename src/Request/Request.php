<?php declare(strict_types=1);

namespace Swiftly\Http\Request;

use Swiftly\Http\CookieCollection;
use Swiftly\Http\Exception\EnvironmentException;
use Swiftly\Http\Exception\SessionException;
use Swiftly\Http\Exception\UrlParseException;
use Swiftly\Http\Header\Accept;
use Swiftly\Http\HeaderCollection;
use Swiftly\Http\Helpers;
use Swiftly\Http\Method;
use Swiftly\Http\ParameterCollection;
use Swiftly\Http\SessionHandler;
use Swiftly\Http\SessionStorageInterface;
use Swiftly\Http\Url;

/**
 * Class used to represent HTTP requests coming into the server.
 *
 * @api
 * @upgrade:php8.1 Swap to readonly properties
 * @psalm-import-type ParameterArray from ParameterCollection
 * @psalm-import-type HttpMethod from Method
 */
class Request
{
    /** @readonly */
    public HeaderCollection $headers;

    /** @readonly */
    public CookieCollection $cookies;

    protected ?SessionHandler $session;

    /** @readonly */
    public ParameterCollection $query;

    /** @readonly */
    public ParameterCollection $post;

    /**
     * @readonly
     * @var non-empty-string
     */
    protected string $method;

    /** @readonly */
    protected URL $url;

    /** Request payload */
    protected ?string $content;

    /**
     * Creates a new Request object from the provided values.
     *
     * The API for the constructor is not part of the backwards compatibility
     * guarantee, please use the {@see self::create()} or
     * {@see self::fromGlobals()} methods instead.
     *
     * @internal
     *
     * @param non-empty-string $method
     */
    public function __construct(
        string $method,
        Url $url,
        HeaderCollection $headers,
        CookieCollection $cookies,
        ParameterCollection $query,
        ParameterCollection $post,
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
     * Returns the HTTP method used for this request.
     *
     * @psalm-mutation-free
     *
     * @return non-empty-string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Returns the protocol of this request.
     *
     * @psalm-mutation-free
     *
     * @return non-empty-string
     */
    public function getProtocol(): string
    {
        return $this->url->protocol;
    }

    /**
     * Returns the host requested by the client.
     *
     * @psalm-mutation-free
     *
     * @return non-empty-string
     */
    public function getHost(): string
    {
        return $this->url->domain;
    }

    /**
     * Returns the URL path of this request.
     *
     * @psalm-mutation-free
     *
     * @return non-empty-string
     */
    public function getPath(): string
    {
        return $this->url->path;
    }

    /**
     * Checks if this request was made via a secure protocol.
     *
     * @psalm-mutation-free
     */
    public function isSecure(): bool
    {
        return $this->url->protocol === 'https';
    }

    /**
     * Checks if this request was made using a known HTTP method.
     *
     * @psalm-mutation-free
     * @psalm-assert-if-true HttpMethod $this->method
     * @psalm-assert-if-true HttpMethod $this->getMethod()
     */
    public function isKnownMethod(): bool
    {
        return Method::isKnownMethod($this->method);
    }

    /**
     * Checks if this request was made using a safe HTTP method.
     *
     * @psalm-mutation-free
     * @psalm-assert-if-true "GET"|"HEAD"|"OPTIONS"|"TRACE" $this->method
     * @psalm-assert-if-true "GET"|"HEAD"|"OPTIONS"|"TRACE" $this->getMethod()
     */
    public function isSafeMethod(): bool
    {
        return Method::isSafeMethod($this->method);
    }

    /**
     * Check if cached responses are allowed to be returned for this request.
     *
     * @psalm-mutation-free
     * @psalm-assert-if-true "GET"|"HEAD" $this->method
     * @psalm-assert-if-true "GET"|"HEAD" $this->getMethod()
     */
    public function allowsCachedResponses(): bool
    {
        return Method::isCacheableMethod($this->method);
    }

    /**
     * Determine if the client would accept the given mime type.
     *
     * @psalm-mutation-free
     *
     * @param string $mimeType The mime type in question.
     */
    public function willAccept(string $mimeType): bool
    {
        $acceptHeader = $this->headers->get(Accept::NAME);

        if (null === $acceptHeader) {
            return true; // rfc2616
        }

        return Accept::fromValue($acceptHeader)->allows($mimeType);
    }

    /**
     * Checks to see is a user session is attached to this request.
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
     * Get the current session handler.
     *
     * @throws SessionException
     *          If no session has been attached to this request.
     *
     * @psalm-mutation-free
     * @psalm-assert SessionHandler $this->session
     */
    public function getSession(): SessionHandler
    {
        if ($this->session === null) {
            throw SessionException::missingFromRequest();
        }

        return $this->session;
    }

    /**
     * Attach a session to this request.
     *
     * @throws SessionException
     *          If a session has already been attached to this request,
     *
     * @psalm-assert null $this->session
     */
    public function setSession(
        SessionHandler|SessionStorageInterface $session,
    ): SessionHandler {
        if ($this->session !== null) {
            throw SessionException::alreadyAssignedToRequest();
        }

        if ($session instanceof SessionStorageInterface) {
            $session = new SessionHandler($session);
        }

        $session->attach($this);

        return $this->session = $session;
    }

    /**
     * Create a new HTTP request with the provided details.
     *
     * @throws UrlParseException
     *          If the value given for `$url` cannot be parsed.
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
        array $post = [],
    ): self {
        return new self(
            $method,
            Url::fromString($url),
            new HeaderCollection($headers),
            new CookieCollection(),
            new ParameterCollection($query),
            new ParameterCollection($post),
        );
    }

    /**
     * Create a new HTTP request using the current PHP globals.
     *
     * @throws EnvironmentException
     *          If PHP global `REQUEST_METHOD` value is undefined.
     *
     * @psalm-mutation-free
     */
    public static function fromGlobals(): self
    {
        if (!isset($_SERVER['REQUEST_METHOD'])) {
            throw EnvironmentException::missingServerVar('REQUEST_METHOD');
        }

        return new self(
            $_SERVER['REQUEST_METHOD'],
            Url::fromGlobals(),
            new HeaderCollection(Helpers::getHeaders()),
            CookieCollection::fromGlobals(),
            new ParameterCollection($_GET),
            new ParameterCollection($_POST),
        );
    }
}
