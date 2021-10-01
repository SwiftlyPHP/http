<?php

namespace Swiftly\Http\Tests\Server;

use Swiftly\Http\Server\RequestFactory;
use Swiftly\Http\Server\Request;
use PHPUnit\Framework\TestCase;

/**
 * @group Shared
 * @backupGlobals enabled
 */
Class RequestFactoryTest Extends TestCase
{

    /** @var RequestFactory $factory */
    private $factory;

    protected function setUp() : void
    {
        $this->factory = new RequestFactory();
    }

    private function exampleGlobals() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/index.php';
        $_SERVER['HTTP_CACHE_CONTROL'] = 'no-cache';
        $_GET = ['id' => 123];
        $_POST = ['name' => 'John'];
    }

    public function testCanCreateRequestFromArgs() : void
    {
        $request = $this->factory->create(
            'POST',
            'https://localhost/index.php',
            [
                'cache-control' => 'no-cache'
            ], [
                'id' => 123
            ], [
                'name' => 'John'
            ]
        );

        self::assertInstanceOf( Request::class, $request );
        self::assertSame( 'POST', $request->getMethod() );
        self::assertSame( 'https', $request->getProtocol() );
        self::assertSame( '/index.php', $request->getPath() );
        self::assertTrue( $request->headers->has( 'cache-control' ) );
        self::assertTrue( $request->query->has( 'id' ) );
        self::assertTrue( $request->post->has( 'name' ) );
    }

    public function testCanCreateRequestFromGlobals() : void
    {
        $this->exampleGlobals();

        $request = $this->factory->fromGlobals();

        self::assertInstanceOf( Request::class, $request );
        self::assertSame( 'POST', $request->getMethod() );
        self::assertSame( 'https', $request->getProtocol() );
        self::assertSame( '/index.php', $request->getPath() );
        self::assertTrue( $request->headers->has( 'cache-control' ) );
        self::assertTrue( $request->query->has( 'id' ) );
        self::assertTrue( $request->post->has( 'name' ) );
    }
}
