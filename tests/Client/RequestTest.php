<?php

namespace Swiftly\Http\Tests\Client;

use Swiftly\Http\Client\Request;
use Swiftly\Http\Client\Response;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
Class RequestTest Extends TestCase
{

    /** @var Request $request */
    private $request;

    protected function setUp() : void
    {
        $this->request = new Request();
    }

    public function testCanMakeGetRequest() : void
    {
        $response = $this->request->get( 'http://localhost' );

        self::assertInstanceOf( Response::class, $response );

        // TODO
    }

    public function testCanMakePostRequest() : void
    {
        $response = $this->request->post( 'http://localhost' );

        self::assertInstanceOf( Response::class, $response );

        // TODO
    }
}
