<?php

namespace Swiftly\Http\Client;

use Swiftly\Http\Headers;
use Swiftly\Http\Url;

/**
 * Class used to make HTTP requests
 *
 * @author clvarley
 */
Class Request
{

    /**
     * Request HTTP headers
     *
     * @var Headers $headers HTTP headers
     */
    public $headers;

    /**
     * URL of this request
     *
     * @var Url $url Request URL
     */
    public $url;

    /**
     * Creates a new outgoing HTTP request
     *
     * @param string $domain                (Optional) Target domain
     * @param array<string,string> $headers (Optional) HTTP headers
     */
    public function __construct( string $domain = '', array $headers = [] )
    {
        $this->headers = new Headers( $headers );
        $this->url = $domain ? Url::fromString( $domain ) : new Url;
    }

    /**
     * Perform a HTTP GET request for the given URL
     *
     * @param string $url                 Target URL
     * @param array<string,string> $query (Optional) Query args
     */
    public function get( string $url, array $query = [] ) : Response
    {
        // TODO
    }

    /**
     * Perform a HTTP POST request for the given URL
     *
     * @param string $url  Target URL
     * @param string $body (Optional) POST payload
     */
    public function post( string $url, string $body = '' ) : Response
    {
        // TODO:
    }
}
