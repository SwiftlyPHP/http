<?php

namespace Swiftly\Http\Tests;

use Swiftly\Http\Parameters;
use PHPUnit\Framework\TestCase;

/**
 * @group Shared
 */
Class ParametersTest Extends TestCase
{

    /** @var Parameters $parameters */
    private $parameters;

    protected function setUp() : void
    {
        $this->parameters = new Parameters([
            'key' => 'value',
            'life' => 42,
            'float' => 42.1,
            'array' => [2, 3, 4]
        ]);
    }

    public function testCanGetParameter() : void
    {
        self::assertSame( 'value', $this->parameters->get( 'key' ) );
        self::assertSame( 42, $this->parameters->get( 'life' ) );
        self::assertNull( $this->parameters->get( 'unknown' ) );
    }

    public function testCanGetAllParameters() : void
    {
        self::assertSame([
            'key' => 'value',
            'life' => 42,
            'float' => 42.1,
            'array' => [2, 3, 4]
        ], $this->parameters->all() );
    }

    public function testCanSetParameter() : void
    {
        $this->parameters->set( 'test', 'example_value' );

        self::assertSame( 'example_value', $this->parameters->get( 'test' ) );
    }

    public function testCanCheckKeyExists() : void
    {
        self::assertTrue( $this->parameters->has( 'key' ) );
        self::assertFalse( $this->parameters->has( 'unknown' ) );
    }

    public function testReturnsParameterAsInt() : void
    {
        self::assertSame( 42, $this->parameters->asInt( 'life' ) );
        self::assertSame( 0, $this->parameters->asInt( 'key' ) );
    }

    public function testReturnsParameterAsString() : void
    {
        self::assertSame( '42.1', $this->parameters->asString( 'float' ) );
        self::assertSame( '', $this->parameters->asString( 'array' ) );
    }

    public function testReturnsParameterAsArray() : void
    {
        self::assertSame( [2, 3, 4], $this->parameters->asArray( 'array' ) );
        self::assertSame( [], $this->parameters->asArray( 'key' ) );
    }
}
