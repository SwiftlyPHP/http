<?php declare(strict_types=1);

namespace Swiftly\Http\Session;

use Swiftly\Http\Exception\SessionException;
use Swiftly\Http\Helpers;
use Swiftly\Http\Request\Request;
use Swiftly\Http\RequestAwareSessionInterface;
use Swiftly\Http\SessionStorageInterface;

use function array_merge;
use function error_get_last;
use function session_name;
use function session_start;
use function session_write_close;

/**
 * Session backed by PHP's native `$_SESSION` store.
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
     * Create a new native session adapter.
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
            'lazy_write' => true,
            'gc_maxlifetime' => (60 * 30) // Half hour
        ], $options);
        $this->request = null;
    }

    /**
     * {@inheritDoc}
     *
     * PHP does not reliably return false for failures from `session_start` so
     * we have to do a bit of wrangling with regard to error notices.
     *
     * When testing across both PHP 7.4 and 8.0 it became apparent that
     * `session_start` will often return true even if it has emitted an error
     * or warning, meaning that we can't just wrap this in an `if` statement as
     * we had previously. Instead, we disable error reporting and compare the
     * error stack before/after starting the session to see if we need to throw.
     */
    public function open(): void
    {
        $previous_error = error_get_last();

        Helpers::suppressErrors(function (): void {
            session_start($this->options);
        });

        $current_error = error_get_last();

        if ($current_error && $current_error !== $previous_error) {
            /** @var array{message:non-empty-string} $current_error */
            throw SessionException::errorOnOpen($current_error['message']);
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
        return isset($_SESSION[$key]);
    }

    /** {@inheritDoc} */
    public function read(string $key): mixed
    {
        return $_SESSION[$key];
    }

    /** {@inheritDoc} */
    public function write(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /** {@inheritDoc} */
    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
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
