<?php

namespace Http\Server;

use Http\{
    Headers,
    Parameters,
    Url
};

/**
 * Class used to represent HTTP requests coming into the server
 *
 * @author C Varley <clvarley>
 */
Class Request
{

    /**
     * Recognised HTTP methods we can respond to
     *
     * We are not currently planning to support the TRACE or CONNECT verbs.
     *
     * @var string[] ALLOWED_METHODS HTTP verbs
     */
    public const ALLOWED_METHODS = [
        'OPTIONS',
        'HEAD',
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE'
    ];

    /**
     * Request HTTP headers
     *
     * @var Headers $headers HTTP headers
     */
    public $headers;

    /**
     * HTTP query string parameters
     *
     * @var Parameters $query Query parameters
     */
    public $query;

    /**
     * HTTP POST parameters
     *
     * @var Parameters $post POST parameters
     */
    public $post;

    /**
     * HTTP method used
     *
     * @var string $method HTTP verb
     */
    protected $method = '';

    /**
     * URL used to generate this request
     *
     * @var Url $url Requested URL
     */
    protected $url;

    /**
     * Request payload
     *
     * @var mixed $content Request body
     */
    protected $content;

    /**
     * Creates a new Request using the provided values
     *
     * We do not recommend creating this object directly, instead please use the
     * RequestFactory class to instantiate new instances of this class.
     *
     * @param string $method    HTTP method
     * @param Url $url          Request URL
     * @param Headers $headers  HTTP headers
     * @param Parameters $query Query parameters
     * @param Parameters $post  Post parameters
     */
    public function __construct( string $method, Url $url, Headers $headers, Parameters $query, Parameters $post )
    {
        $this->method = \in_array( $method, static::ALLOWED_METHODS ) ? $method : 'GET';
        $this->url = $url;
        $this->headers = $headers;
        $this->query = $query;
        $this->post = $post;
    }
}
