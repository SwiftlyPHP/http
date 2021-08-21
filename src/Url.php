<?php

namespace Swiftly\Http;

use Stringable;

use function in_array;
use function parse_url;

/**
 * Utility class for representing URLs
 *
 * @author clvarley
 */
Class Url Implements Stringable
{

    /**
     * The protocol being used
     *
     * @var string $scheme Url scheme
     */
    public $scheme = '';

    /**
     * The requested authority/domain
     *
     * @var string $domain Domain component
     */
    public $domain = '';

    /**
     * Path to resource
     *
     * @var string $path Path component
     */
    public $path = '';

    /**
     * Additional query parameters
     *
     * @var string $query Query string
     */
    public $query = '';

    /**
     * Resource fragment
     *
     * @var string $fragment Fragment identifier
     */
    public $fragment = '';

    /**
     * Returns the string representation of this URL
     *
     * @return string Url
     */
    public function __toString() : string
    {
        $url = "{$this->scheme}://{$this->domain}{$this->path}";

        if ( !empty( $this->query ) ) {
            $url .= "?{$this->query}";
        }

        if ( !empty( $this->fragment ) ) {
            $url .= "#{$this->fragment}";
        }

        return $url;
    }

    /**
     * Determine whether this URL is valid
     *
     * @return bool Is valid?
     */
    public function valid() : bool
    {
        return ( in_array( $this->scheme, [ 'http', 'https' ] )
            && !empty( $this->domain ) );
    }

    /**
     * Attempt to parse the given string into a Url object
     *
     * @param string $url Subject string
     * @return Url        Url object
     */
    public static function fromString( string $url ) : Url
    {
        $parts = parse_url( $url );

        // NOTE: Possibly throw exception?
        if ( $parts === false ) {
            $parts = [];
        }

        $url = new Url;

        // Always assume the least secure!
        $url->scheme   = $parts['scheme']   ?? 'http';
        $url->domain   = $parts['host']     ?? '';
        $url->path     = $parts['path']     ?? '';
        $url->query    = $parts['query']    ?? '';
        $url->fragment = $parts['fragment'] ?? '';

        return $url;
    }

    /**
     * Creates a Url object from the global $_SERVER variable
     *
     * @return Url Url object
     */
    public static function fromGlobals() : Url
    {
        // Connection protocol
        if ( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ) {
            $scheme = 'https';
        } elseif ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] )
            && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
        ) {
            $scheme = 'https';
        } else {
            $scheme = 'http';
        }

        return self::fromString(
            "$scheme://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"
        );
    }
}
