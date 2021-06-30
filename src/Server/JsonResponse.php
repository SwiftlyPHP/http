<?php

namespace Swiftly\Http\Server;

use Swiftly\Http\Headers;

use function json_encode;
use function strlen;

use const JSON_HEX_AMP;
use const JSON_HEX_TAG;
use const JSON_HEX_APOS;
use const JSON_HEX_QUOT;

/**
 * Class used to send JSON responses to the client
 *
 * @author clvarley
 */
Class JsonResponse Extends Response
{

    /**
     * Array representation of JSON content
     *
     * @var array $json JSON content
     */
    protected $json;

    /**
     * Encoding options used during json_encode
     *
     * @var int $encoding Encoding options
     */
    protected $encoding = JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT;

    /**
     * Creates a new JSON HTTP response using the values provided
     *
     * @param array $json                   (Optional) Response content
     * @param int $status                   (Optional) Status code
     * @param array<string,string> $headers (Optional) Http headers
     */
    public function __construct( array $json = [], int $status = 200, array $headers = [] )
    {
        $this->json = $json;

        parent::__construct( '', $status, $headers );
    }

    /**
     * Sets the JSON content of this response
     *
     * @param array $json Response content
     * @return void       N/a
     */
    public function setJson( array $json ) : void
    {
        $this->json = $json;
    }

    /**
     * Sets the encoding options used during json_encode
     *
     * @param int $encoding Encoding options
     * @return void         N/a
     */
    public function setEncoding( int $encoding ) : void
    {
        $this->encoding = $encoding;
    }

    /**
     * {@inheritdoc}
     */
    public function send() : void
    {
        $this->content = json_encode( $this->json, $this->encoding );
        $this->headers->set( 'Content-Type', 'application/json' );
        $this->headers->set( 'Content-Length', (string)strlen( $this->content ) );

        parent::send();
    }
}
