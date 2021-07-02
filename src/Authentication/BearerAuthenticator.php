<?php

namespace Swiftly\Http\Authentication;

use Swiftly\Http\AuthenticationInterface;
use Swiftly\Http\Client\Request;

/**
 * Handles bearer/token based authentication
 *
 * @author clvarley
 */
Class BearerAuthenticator Implements AuthenticationInterface
{

    /**
     * Bearer token for the request
     *
     * @var string $token Bearer token
     */
    private $token;

    /**
     * Authenticates requests using the provided token
     *
     * @param string $token Bearer token
     */
    public function __construct( string $token )
    {
        $this->token = $token;
    }

    /**
     * Authenticates the request by adding the required header
     *
     * @param Request $request Outgoing request
     * @return Request         Authenticated request
     */
    public function authenticate( Request $request ) : Request
    {
        $request->headers->set( 'Authentication', "Bearer {$this->token}" );

        return $request;
    }
}
