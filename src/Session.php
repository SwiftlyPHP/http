<?php

namespace Swiftly\Http;

use Swiftly\Http\SessionInterface;

/**
 * Class used to wrap underlying session adapter
 */
Class Session
{

    /**
     * Underlying store/adapter for this session
     *
     * @readonly
     * @var SessionInterface $adapter Session adapter
     */
    private $adapter;

    /**
     * Create a new session using the given adapter
     *
     * @param SessionInterface $adapter Session adapter
     */
    public function __construct( SessionInterface $adapter )
    {
        $this->adapter = $adapter;
    }

    /**
     * Open this session
     *
     * @return bool Session opened?
     */
    public function open() : bool
    {
        $this->adapter->open();
    }

    /**
     * Close this session
     *
     * @return bool Session closed?
     */
    public function close() : bool
    {
        $this->adapter->close();
    }
}
