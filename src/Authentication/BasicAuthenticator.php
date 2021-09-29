<?php

namespace Swiftly\Http\Authentication;

use Swiftly\Http\AuthenticationInterface;
use Swiftly\Http\Client\Request;

use function base64_encode;

/**
 * Handles basic access authentication using a username and password
 *
 * @author clvarley
 */
Class BasicAuthenticator Implements AuthenticationInterface
{

    /**
     * Auth value to be used in the request header
     *
     * @readonly
     * @var string $auth Auth header value
     */
    private $auth;

    /**
     * Authenticates requests using the basic access auth scheme
     *
     * @param string $username Auth username
     * @param string $password Auth password
     */
    public function __construct( string $username, string $password )
    {
        $this->auth = base64_encode( "$username:$password" );
    }

    /**
     * Authenticates the request by adding the required header
     *
     * @param Request $request Outgoing request
     * @return Request         Authenticated request
     */
    public function authenticate( Request $request ) : Request
    {
        $request->headers->set( 'Authorization', "Basic {$this->auth}" );

        return $request;
    }
}
