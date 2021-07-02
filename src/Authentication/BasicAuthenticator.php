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
     * Username to use for the request
     *
     * @var string $username Plaintext username
     */
    private $username;

    /**
     * Password to use for the request
     *
     * @var string $password Plaintext password
     */
    private $password;

    /**
     * Authenticates requests using the basic access auth scheme
     *
     * @param string $username Auth username
     * @param string $password Auth password
     */
    public function __construct( string $username, string $password )
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Authenticates the request by adding the required header
     *
     * @param Request $request Outgoing request
     * @return Request         Authenticated request
     */
    public function authenticate( Request $request ) : Request
    {
        $auth = base64_encode( "{$this->username}:{$this->password}" );

        $request->headers->set( 'Authorization', "Basic $auth" );

        return $request;
    }
}
