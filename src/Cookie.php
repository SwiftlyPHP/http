<?php declare(strict_types=1);

namespace Swiftly\Http;

use function time;

/**
 * Class used to represent and manage a single HTTP cookie
 *
 * @api
 */
class Cookie
{
    private const DAY_IN_SECONDS = 60 * 60 * 24;

    /** @var non-empty-string */
    public string $name;

    public string $value;

    /** Expiry time as unix timestamp */
    public int $expires = 0;

    /** Allowed (sub)path */
    public string $path = '';

    /** Allowed (sub)domain */
    public string $domain = '';

    public bool $secure = true;

    public bool $httponly = false;

    /** @internal */
    private bool $isModified = false;

    /**
     * Create a new cookie with the given name and value.
     *
     * @param non-empty-string $name
     */
    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Invalidate this cookie.
     *
     * We want to remove this from the client, so we clear the value and set the
     * expiry date in the past. Most spec conforming browsers will then delete
     * their local copy.
     */
    public function invalidate(): void
    {
        $this->value = '';
        $this->expires = time() - self::DAY_IN_SECONDS;
        $this->isModified = true;
    }

    /**
     * Flag this cookie as modified, indicating it should be sent to the client.
     */
    public function touch(): void
    {
        $this->isModified = true;
    }

    /**
     * Determine if this cookie has been modified and thus should be sent.
     */
    public function isModified(): bool
    {
        return $this->isModified;
    }
}
