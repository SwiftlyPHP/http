<?php

namespace Swiftly\Http;

/**
 * Interface for HTTP capable network adapters
 *
 * @author clvarley
 */
Interface TransportInterface
{

    /**
     * Sends a HTTP GET request
     *
     * @param string $url       Target URL
     * @param string[] $headers (Optional) HTTP headers
     * @return string           Response content
     * @return Response
     */
    public function get( string $url, array $headers = [] ) : Response;

    /**
     * Sends a HTTP POST request
     *
     * @param string $url       Target URL
     * @param string[] $headers (Optional) HTTP headers
     * @param string $body      (Optional) Request body
     * @return string           Response content
     * @return Response
     */
    public function post( string $url, array $headers = [], string $body = '' ) : Response;

    /**
     * Sends a HTTP request with a custom method
     *
     * @param string $method    HTTP method
     * @param string $url       Target URL
     * @param string[] $headers (Optional) HTTP headers
     * @param string $body      (Optional) Request body
     * @return Response
     */
    public function send( string $method, string $url, array $headers = [], string $body = '' ) : Response;

}
