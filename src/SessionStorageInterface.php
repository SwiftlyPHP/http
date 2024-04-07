<?php declare(strict_types=1);

namespace Swiftly\Http;

/**
 * Contract for classes that are capable of storing user session data
 *
 * @api
 */
interface SessionStorageInterface
{
    /**
     * Open this session for read/writes
     */
    public function open(): void;
    /**
     * Close this session
     */
    public function close(): void;
    /**
     * Return the name of the cookie used to store the session ID
     *
     * @return non-empty-string Cookie name
     */
    public function name(): string;
    /**
     * Check if a given key has associated value
     *
     * @psalm-assert-if-true !null $this->read($key)
     * @param non-empty-string $key Data key
     * @return bool                 Key has value
     */
    public function has(string $key): bool;
    /**
     * Read a value from the session
     *
     * @php:8.0 Add union return type hint
     * @psalm-assert true $this->has()
     * @param non-empty-string $key Data key
     * @return mixed                Data value
     */
    public function read(string $key);
    /**
     * Write a value to the session
     *
     * @php:8.0 Added union type hint
     * @param non-empty-string $key Data key
     * @param scalar|array $value   Data value
     */
    public function write(string $key, $value): void;
    /**
     * Remove a named value from the session
     *
     * @param non-empty-string $key Data key
     */
    public function remove(string $key): void;
    /**
     * Clear all values from the session
     */
    public function clear(): void;
    /**
     * Persist any session updates to storage
     */
    public function persist(): void;
    /**
     * Destroy this session, removing all references to it
     */
    public function destroy(): void;
}
