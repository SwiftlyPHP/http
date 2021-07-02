<?php

namespace Swiftly\Http;

use Swiftly\Http\Client\Request;

/**
 * Interface for classes capable of authenticating HTTP requests
 *
 * @author clvarley
 */
Interface AuthenticationInterface
{

    /**
     * Authenticate the given HTTP request
     *
     * @param Request $request Outgoing request
     * @return void            N/a
     */
    public function authenticate( Request $request ) : void;

}
