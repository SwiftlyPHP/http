<?php declare(strict_types=1);

namespace Swiftly\Http;

/**
 * Contract for classes that are capable of storing user session data.
 *
 * @api
 */
interface SessionStorageInterface
{
    /**
     * Open this session for read/writes.
     */
    public function open(): void;

    /**
     * Close this session.
     */
    public function close(): void;

    /**
     * Return the name of the cookie used to store the session ID.
     *
     * @return non-empty-string
     */
    public function name(): string;

    /**
     * Check if a given key has associated value.
     *
     * @psalm-assert-if-true !null $this->read($key)
     *
     * @param non-empty-string $key
     */
    public function has(string $key): bool;

    /**
     * Read a value from the session.
     *
     * @psalm-assert true $this->has()
     *
     * @param non-empty-string $key
     */
    public function read(string $key): mixed;

    /**
     * Write a value to the session.
     *
     * @param non-empty-string $key
     * @param scalar|array $value
     */
    public function write(string $key, $value): void;

    /**
     * Remove a named value from the session.
     *
     * @param non-empty-string $key
     */
    public function remove(string $key): void;

    /**
     * Clear all values from the session.
     */
    public function clear(): void;

    /**
     * Persist any session updates to storage.
     */
    public function persist(): void;

    /**
     * Destroy this session, removing all references to it.
     */
    public function destroy(): void;
}
