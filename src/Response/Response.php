<?php declare(strict_types=1);

namespace Swiftly\Http\Response;

use Swiftly\Http\HeaderCollection;
use Swiftly\Http\CookieCollection;
use Swiftly\Http\Status;

/**
 * Class used to send HTTP responses to the client
 *
 * @api
 * @upgradephp8.1 Swap to readonly properties
 */
class Response
{
    /** @readonly */
    public HeaderCollection $headers;

    /** @readonly */
    public CookieCollection $cookies;

    /** @var Status::* */
    protected int $status;

    /** Response payload */
    protected string $content;

    /**
     * Creates a new HTTP response using the values provided.
     *
     * @param Status::* $status
     * @param array<non-empty-string,string> $headers
     */
    public function __construct(
        int $status = Status::OK,
        string $content = '',
        array $headers = [],
    ) {
        $this->status  = $status;
        $this->content = $content;
        $this->headers = new HeaderCollection($headers);
        $this->cookies = new CookieCollection();
    }

    /**
     * Get the HTTP status code of this response.
     *
     * @psalm-return Status::*
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Set the HTTP status code of this response.
     *
     * @psalm-param Status::* $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * Get the content of this response.
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Set the content of this response.
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * Set the content type of this response.
     */
    public function setContentType(string $type): void
    {
        $this->headers->set('Content-Type', $type);
    }

    /**
     * Returns the content type of this response.
     */
    public function getContentType(): string
    {
        return $this->headers->get('Content-Type') ?? 'text/plain';
    }
}
