<?php

namespace Swiftly\Http\Session;

use Swiftly\Http\SessionStorageInterface;
use Swiftly\Http\Exception\SessionException;
use Swiftly\Http\RequestAwareSessionInterface;
use Swiftly\Http\Request\Request;

use function array_merge;
use function session_start;
use function session_name;
use function session_write_close;

/**
 * Session backed by PHP's native `$_SESSION` store
 *
 * @api
 */
class NativeSession implements
    SessionStorageInterface,
    RequestAwareSessionInterface
{
    private array $options;

    private ?Request $request;

    /**
     * Create a new native session adapter
     *
     * Provided options will be passed directly to PHP's {@see session_start()}
     * function.
     *
     * @param array<non-empty-string,string|int|bool> $options Session options
     */
    public function __construct(array $options = [])
    {
        $this->options = array_merge([
            'use_strict_mode' => true,
            'use_only_cookies' => true,
            'cookie_secure' => true,
            'cookie_httponly' => true,
            'cookie_samesite' => 'Strict',
            'use_trans_sid' => false,
            'sid_length' => 64,
            'lazy_write' => true,
            'gc_maxlifetime' => (60 * 24)
        ], $options);
        $this->request = null;
    }

    /** {@inheritDoc} */
    public function open(): void
    {
        if (session_start($this->options) === false) {
            throw new SessionException('open', 'internal PHP error');
        }
    }

    /** {@inheritDoc} */
    public function close(): void
    {
        session_write_close();
    }

    /** {@inheritDoc} */
    public function name(): string
    {
        return session_name() ?: 'PHPSESSID';
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-assert-if-true !null $_SERVER[$key]
     */
    public function has(string $key): bool
    {
        return isset($_SERVER[$key]);
    }

    /** {@inheritDoc} */
    public function read(string $key)
    {
        return $_SERVER[$key];
    }

    /** {@inheritDoc} */
    public function write(string $key, $value): void
    {
        $_SERVER[$key] = $value;
    }

    /** {@inheritDoc} */
    public function remove(string $key): void
    {
        unset($_SERVER[$key]);
    }

    /** {@inheritDoc} */
    public function clear(): void
    {
        $_SESSION = [];
    }

    /** no-op */
    public function persist(): void
    {
        return;
    }

    /** {@inheritDoc} */
    public function destroy(): void
    {
        $this->clear();

        if ($this->request !== null) {
            $this->request->cookies->remove($this->name());
        }
    }

    /** {@inheritDoc} */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }
}
