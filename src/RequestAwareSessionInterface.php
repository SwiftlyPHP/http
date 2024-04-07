<?php declare(strict_types=1);

namespace Swiftly\Http;

use Swiftly\Http\Exception\SessionException;
use Swiftly\Http\Request\Request;

/**
 * Optional interface for session adapters to flag they are request aware
 *
 * @see \Swiftly\Http\SessionStorageInterface
 *
 * @api
 */
interface RequestAwareSessionInterface
{
    /**
     * Associate this session with the given HTTP request
     *
     * @throws SessionException
     *          Implementers can throw if an unrecoverable error occurs
     *
     * @param Request $request User HTTP request
     */
    public function setRequest(Request $request): void;
}
