<?php

namespace Swiftly\Http\Server;

use Swiftly\Http\Server\Request;
use Swiftly\Http\Url;
use Swiftly\Http\Headers;
use Swiftly\Http\Cookies;
use Swiftly\Http\Parameters;

use function apache_request_headers;

/**
 * Factory used to create Request objects
 *
 * @author clvarley
 */
Class RequestFactory
{

    /**
     * Creates a Request object from the values provided
     *
     * @param string $method                 HTTP method
     * @param string $url                    Request URL
     * @param array<string, string> $headers HTTP headers
     * @param array $query                   Query parameters
     * @param array $post                    POST parameters
     * @return Request                       Request object
     */
    public function create( string $method = 'GET', string $url = '', array $headers = [], array $query = [], array $post = [] ) : Request
    {
        return new Request(
            $method,
            Url::fromString( $url ),
            new Headers( $headers ),
            new Cookies(), // TODO
            new Parameters( $query ),
            new Parameters( $post )
        );
    }

    /**
     * Creates a Request object from the global request values
     *
     * @global array $_SERVER
     * @return Request Request object
     */
    public function fromGlobals() : Request
    {
        return new Request(
            (string)$_SERVER['REQUEST_METHOD'],
            Url::fromGlobals(),
            new Headers( apache_request_headers() ),
            new Cookies(), // TODO
            new Parameters( $_GET ),
            new Parameters( $_POST )
        );
    }
}
