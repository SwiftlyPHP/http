<?php

namespace Swiftly\Http\Authentication;

use Swiftly\Http\AuthenticationInterface;

/**
 * Handles basic access authentication using a username and password
 *
 * @author clvarley
 */
Class BasicAuthenticator Implements AuthenticationInterface
{

    /**
     * Username to use for this request
     *
     * @var string $username Auth username
     */
    private $username;

    /**
     * Password to use for this request
     *
     * @var string $password Auth password
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

    
}
