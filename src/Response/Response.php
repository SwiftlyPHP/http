<?php declare(strict_types=1);

namespace Swiftly\Http\Response;

use Swiftly\Http\HeaderCollection;
use Swiftly\Http\CookieCollection;
use Swiftly\Http\Status;

/**
 * Class used to send HTTP responses to the client
 *
 * @api
 * @php:8.1 Swap to readonly properties
 */
class Response
{
    /**
     * Response HTTP headers
     *
     * @readonly
     */
    public HeaderCollection $headers;

    /**
     * Response HTTP cookie
     *
     * @readonly
     */
    public CookieCollection $cookies;

    /**
     * Response status code
     *
     * @psalm-var Status::* $status
     */
    protected int $status;

    /** Response payload */
    protected string $content;

    /**
     * Creates a new HTTP response using the values provided
     *
     * @psalm-param Status::* $status
     *
     * @param int $status                             Status code
     * @param string $content                         Response body
     * @param array<non-empty-string,string> $headers HTTP header values
     */
    public function __construct(
        int $status = Status::OK,
        string $content = '',
        array $headers = []
    ) {
        $this->status  = $status;
        $this->content = $content;
        $this->headers = new HeaderCollection($headers);
        $this->cookies = new CookieCollection();
    }

    /**
     * Gets the status code of this response
     *
     * @psalm-return Status::*
     *
     * @return int Status code
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Sets the status code of this response
     *
     * @psalm-param Status::* $status
     *
     * @param int $status Status code
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * Gets the content of this response
     *
     * @return string Response body
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Sets the content of this response
     *
     * @param string $content Response body
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * Sets the content type of this response
     *
     * @param string $type Content type
     */
    public function setContentType(string $type): void
    {
        $this->headers->set('Content-Type', $type);
    }

    /**
     * Returns the content type of this response
     *
     * @return string $type Content type
     */
    public function getContentType(): string
    {
        return $this->headers->get('Content-Type') ?? 'text/plain';
    }
}
