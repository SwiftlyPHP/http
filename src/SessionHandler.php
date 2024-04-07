<?php declare(strict_types=1);

namespace Swiftly\Http;

use Swiftly\Http\SessionStorageInterface;
use Swiftly\Http\Exception\SessionException;
use Swiftly\Http\Exception\SessionReadException;
use Swiftly\Http\Exception\SessionWriteException;
use Swiftly\Http\Request\Request;
use Swiftly\Http\RequestAwareSessionInterface;

/**
 * Handler used to manage the lifecycle of a user session
 *
 * @php:8.1 Use readonly properties
 * @api
 */
class SessionHandler
{
    private const SESSION_UNOPENED = 0;
    private const SESSION_OPEN = 1;
    private const SESSION_CLOSED = 2;

    /**
     * Backing storage adapter
     *
     * @readonly
     */
    private SessionStorageInterface $storage;

    /** @var self::* $state Current session status */
    private int $state;

    /**
     * Create a new session handler using the underlying storage mechanism
     *
     * @param SessionStorageInterface $storage Session storage adapter
     */
    public function __construct(SessionStorageInterface $storage)
    {
        $this->storage = $storage;
        $this->state = self::SESSION_UNOPENED;
    }

    /**
     * Make sure session data is persisted if this object goes out of scope
     */
    public function __destruct()
    {
        if ($this->isOpen()) {
            $this->close();
        }
    }

    /**
     * Check whether this session is currently open
     *
     * @psalm-mutation-free
     * @psalm-assert-if-true self::SESSION_OPEN $this->state
     * @return bool Session is open
     */
    public function isOpen(): bool
    {
        return $this->state === self::SESSION_OPEN;
    }

    /**
     * Check whether this session has been closed
     *
     * @psalm-mutation-free
     * @psalm-assert-if-true self::SESSION_CLOSED $this->state
     * @return bool Session is closed
     */
    public function isClosed(): bool
    {
        return $this->state === self::SESSION_CLOSED;
    }

    /**
     * Opens the session for reading and writing
     *
     * @psalm-assert self::SESSION_UNOPENED $this->state
     */
    public function open(): void
    {
        if ($this->state === self::SESSION_OPEN) {
            throw self::exception('open', 'session is already open');
        }

        if ($this->state === self::SESSION_CLOSED) {
            throw self::exception('open', 'session is already closed');
        }

        $this->storage->open();
        $this->state = self::SESSION_OPEN;
    }

    /**
     * Closes the session, persisting to storage stopping future read/writes
     *
     * @psalm-assert self::SESSION_OPEN $this->state
     */
    public function close(): void
    {
        if ($this->state === self::SESSION_UNOPENED) {
            throw new SessionException('close', 'session is not open');
        }

        if ($this->state === self::SESSION_CLOSED) {
            throw new SessionException('close', 'session is already closed');
        }

        $this->storage->persist();
        $this->storage->close();
        $this->state = self::SESSION_CLOSED;
    }

    /**
     * Check if data has been set for a given key
     *
     * @psalm-assert self::SESSION_UNOPENED|self::SESSION_OPEN $this->state
     * @param non-empty-string $key Data key
     * @return bool                 Key has data
     */
    public function has(string $key): bool
    {
        $this->prepare('read');

        return $this->storage->has($key);
    }

    /**
     * Read data from the session with the given key
     *
     * @php:8.0 Add union return type
     * @psalm-assert self::SESSION_UNOPENED|self::SESSION_OPEN $this->state
     * @param non-empty-string $key Data key
     * @return mixed                Data value
     */
    public function get(string $key)// : mixed
    {
        $this->prepare('read');

        return $this->storage->read($key);
    }

    /**
     * Write data to the session with the given key
     *
     * @psalm-assert self::SESSION_OPEN $this->state
     * @param non-empty-string $key Data key
     * @param scalar|array $value   Data value
     */
    public function set(string $key, $value): void
    {
        $this->prepare('write');

        $this->storage->write($key, $value);
    }

    /**
     * Removed data associated with a given key
     *
     * @psalm-assert self::SESSION_OPEN $this->state
     * @param non-empty-string $key Data key
     */
    public function remove(string $key): void
    {
        $this->prepare('remove');

        $this->storage->remove($key);
    }

    /**
     * Clears all data in the session - effectively deleting it
     *
     * @psalm-assert self::SESSION_OPEN $this->state
     */
    public function clear(): void
    {
        $this->prepare('clear');

        $this->storage->clear();
    }
    
    /**
     * Completely destroy a session and all its associated data
     */
    public function destroy(): void
    {
        $this->storage->destroy();
    }

    /**
     * Attach this session to the given request
     *
     * @throws SessionException
     *          If the session adapter encounters an error while attaching
     *
     * @param Request $request User request
     */
    public function attach(Request $request): void
    {
        if ($this->storage instanceof RequestAwareSessionInterface) {
            $this->storage->setRequest($request);
        }
    }

    /**
     * Performs setup, preparing session for reading and writing
     *
     * @throws SessionReadException
     *          If an error occurred while attempting to read
     * @throws SessionWriteException
     *          If an error occurred while attempt to write
     * @throws SessionException
     *          If a more general error occurred
     *
     * @php:8.0 Swap to match statement
     * @psalm-assert self::SESSION_OPEN $this->state
     * @param non-empty-string $context Context message (for error reporting)
     */
    private function prepare(string $context): void
    {
        switch ($this->state) {
            case self::SESSION_UNOPENED:
                $this->open();
            case self::SESSION_OPEN:
                return;
            case self::SESSION_CLOSED:
                throw self::exception($context, 'session is already closed');
        }
    }

    /**
     * Return the appropriate exception class for the given context
     *
     * @php:8.0 Swap to match statement
     * @psalm-assert self::SESSION_UNOPENED|self::SESSION_OPEN $this->state
     * @psalm-return ($context is 'read' ? SessionReadException
     *          : ($context is 'write' ? SessionWriteException
     *              : SessionException))
     * @param non-empty-string $context Error context
     * @param non-empty-string $message Error message
     * @return SessionException         Session exception
     */
    private static function exception(
        string $context,
        string $message
    ): SessionException {
        switch ($context) {
            case 'read':
                return new SessionReadException($message);
            case 'write':
                return new SessionWriteException($message);
            default:
                return new SessionException($context, $message);
        }
    }
}
