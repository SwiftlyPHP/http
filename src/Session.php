<?php

namespace Swiftly\Http;

use Swiftly\Http\SessionInterface;

/**
 * Class used to wrap underlying session adapter
 */
class Session
{
    /**
     * Underlying store/adapter for this session
     *
     * @readonly
     * @var SessionInterface $adapter Session adapter
     */
    private $adapter;

    /**
     * Is this session currently open?
     *
     * @var bool $is_open Session open?
     */
    private $is_open = false;

    /**
     * Create a new session using the given adapter
     *
     * @param SessionInterface $adapter Session adapter
     */
    public function __construct(SessionInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Open this session
     *
     * @return bool Session opened?
     */
    public function open(): bool
    {
        if (!$this->is_open) {
            $this->is_open = $this->adapter->open();
        }

        return $this->is_open;
    }

    /**
     * Close this session
     *
     * @return bool Session closed?
     */
    public function close(): bool
    {
        if ($this->is_open) {
            $this->is_open = !$this->adapter->close();
        }

        return $this->is_open;
    }

    /**
     * Return the current status of the session
     *
     * @return bool Session opened?
     */
    public function isOpen(): bool
    {
        return $this->is_open;
    }
}
