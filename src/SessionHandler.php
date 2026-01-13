<?php declare(strict_types=1);

namespace Swiftly\Http;

use Swiftly\Http\Exception\SessionException;
use Swiftly\Http\Request\Request;

/**
 * Handler used to manage the lifecycle of a user session.
 *
 * @api
 * @upgrade:php8.1 Use readonly properties
 */
class SessionHandler
{
    private const SESSION_UNOPENED = 0;
    private const SESSION_OPEN = 1;
    private const SESSION_CLOSED = 2;

    private const OP_READ = 'read';
    private const OP_WRITE = 'write';
    private const OP_DELETE = 'delete';

    /** @readonly */
    private SessionStorageInterface $storage;

    /** @var self::SESSION_* */
    private int $state;

    /**
     * Create a new session handler using the underlying storage mechanism.
     */
    public function __construct(SessionStorageInterface $storage)
    {
        $this->storage = $storage;
        $this->state = self::SESSION_UNOPENED;
    }

    /**
     * Make sure session data is persisted if this object goes out of scope.
     */
    public function __destruct()
    {
        if ($this->isOpen()) {
            $this->close();
        }
    }

    /**
     * Check whether this session is currently open.
     *
     * @psalm-mutation-free
     * @psalm-assert-if-true self::SESSION_OPEN $this->state
     */
    public function isOpen(): bool
    {
        return $this->state === self::SESSION_OPEN;
    }

    /**
     * Check whether this session has been closed.
     *
     * @psalm-mutation-free
     * @psalm-assert-if-true self::SESSION_CLOSED $this->state
     */
    public function isClosed(): bool
    {
        return $this->state === self::SESSION_CLOSED;
    }

    /**
     * Opens the session for reading and writing.
     *
     * @psalm-assert self::SESSION_UNOPENED $this->state
     */
    public function open(): void
    {
        if ($this->state === self::SESSION_OPEN) {
            throw SessionException::invalidOpenCall();
        }
        if ($this->state === self::SESSION_CLOSED) {
            throw SessionException::cannotReopenWhenClosed();
        }

        $this->storage->open();
        $this->state = self::SESSION_OPEN;
    }

    /**
     * Closes the session, persisting all data and halting further read/writes.
     *
     * @psalm-assert self::SESSION_OPEN $this->state
     */
    public function close(): void
    {
        if ($this->state === self::SESSION_CLOSED) {
            throw SessionException::invalidCloseCall();
        }
        if ($this->state === self::SESSION_UNOPENED) {
            throw SessionException::cannotCloseWhenUnopened();
        }

        $this->storage->persist();
        $this->storage->close();
        $this->state = self::SESSION_CLOSED;
    }

    /**
     * Check if data has been set for a given key.
     *
     * @psalm-assert self::SESSION_UNOPENED|self::SESSION_OPEN $this->state
     *
     * @param non-empty-string $key
     */
    public function has(string $key): bool
    {
        return $this
            ->getOpenSession(self::OP_READ)
            ->has($key);
    }

    /**
     * Read data from the session with the given key.
     *
     * @psalm-assert self::SESSION_UNOPENED|self::SESSION_OPEN $this->state
     *
     * @param non-empty-string $key
     */
    public function get(string $key): mixed
    {
        return $this
            ->getOpenSession(self::OP_READ)
            ->read($key);
    }

    /**
     * Write data to the session with the given key.
     *
     * @psalm-assert self::SESSION_OPEN $this->state
     *
     * @param non-empty-string $key
     * @param scalar|array $value
     */
    public function set(string $key, mixed $value): void
    {
        $this
            ->getOpenSession(self::OP_WRITE)
            ->write($key, $value);
    }

    /**
     * Removed data associated with a given key.
     *
     * @psalm-assert self::SESSION_OPEN $this->state
     *
     * @param non-empty-string $key
     */
    public function remove(string $key): void
    {
        $this
            ->getOpenSession(self::OP_DELETE)
            ->remove($key);
    }

    /**
     * Clears all data in the session - effectively deleting it.
     *
     * @psalm-assert self::SESSION_OPEN $this->state
     */
    public function clear(): void
    {
        $this
            ->getOpenSession(self::OP_DELETE)
            ->clear();
    }

    /**
     * Completely destroy a session and all its associated data.
     */
    public function destroy(): void
    {
        $this
            ->getOpenSession(self::OP_DELETE)
            ->destroy();
    }

    /**
     * Attach this session to the given request.
     *
     * @throws SessionException
     *          If the session adapter encounters an error while attaching.
     */
    public function attach(Request $request): void
    {
        if ($this->storage instanceof RequestAwareSessionInterface) {
            $this->storage->setRequest($request);
        }
    }

    /**
     * @throws SessionException
     *
     * @psalm-assert self::SESSION_OPEN $this->state
     * @psalm-assert true $this->isOpen()
     * @psalm-assert false $this->isClosed()
     *
     * @param self::OP_* $operation
     */
    private function getOpenSession(string $operation): SessionStorageInterface
    {
        switch ($this->state) {
            case self::SESSION_CLOSED:
                throw SessionException::alreadyClosed($operation);
            case self::SESSION_UNOPENED:
                $this->open();
                // no break
            case self::SESSION_OPEN:
        };

        return $this->storage;
    }
}
