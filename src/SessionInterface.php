<?php

namespace Swiftly\Http;

/**
 * Interface for session adapters
 *
 * @author clvarley
 */
interface SessionInterface
{
    /**
     * Attempt to open a new (or existing) session
     *
     * @return bool Session opened?
     */
    public function open(): bool;

    /**
     * Attempt to close the current session
     *
     * @return bool Session closed?
     */
    public function close(): bool;
}
