<?php

namespace Http\Server;

use Http\Headers;

/**
 * Class used to send HTTP responses to the client
 *
 * @author C Varley <clvarley>
 */
Class Response
{

    /**
     * Response HTTP headers
     *
     * @var Headers $headers HTTP headers
     */
    public $headers;

    /**
     * Response status code
     *
     * @var int $status Status code
     */
    protected $status;

    /**
     * Response payload
     *
     * @var string $content Response body
     */
    protected $content = '';

    /**
     * Creates a new HTTP response using the values provided
     *
     * @param int $status     (Optional) Status code
     * @param string $content (Optional) Response body
     * @param array $headers  (Optional) Http headers
     */
    public function __construct( int $status = 200, string $content = '', array $headers = [] )
    {
        $this->status  = $status;
        $this->content = $content;
        $this->headers = new Headers( $headers );
    }

    /**
     * Sets the status code of this response
     *
     * @param int $status Status code
     * @return void       N/a
     */
    public function setStatus( int $status ) : void
    {
        $this->status = $status;
    }

    /**
     * Sets the content of this response
     *
     * @param string $content Response body
     * @return void           N/a
     */
    public function setContent( string $content ) : void
    {
        $this->content = $content;
    }

    /**
     * Sends this HTTP response to the client
     *
     * @return void N/a
     */
    public function send() : void
    {
        \http_response_code( $this->status );

        foreach ( $this->headers->all() as $name => $values ) {
            foreach ( $values as $index => $value ) {
                \header( "$name: $value", $index === 0 );
            }
        }

        echo $this->content;
    }
}
