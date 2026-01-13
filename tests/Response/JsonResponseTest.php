<?php declare(strict_types=1);

namespace Swiftly\Http\Tests\Response;

use PHPUnit\Framework\TestCase;
use Swiftly\Http\Response\JsonResponse;
use Swiftly\Http\Status;

use const JSON_PRESERVE_ZERO_FRACTION;

/**
 * @covers \Swiftly\Http\Response\JsonResponse
 * @covers \Swiftly\Http\Response\Response
 * @uses \Swiftly\Http\HeaderCollection
 * @uses \Swiftly\Http\CookieCollection
 */
final class JsonResponseTest extends TestCase
{
    private JsonResponse $response;

    public function setUp(): void
    {
        $this->response = new JsonResponse([
            'user' => 'Jane',
            'age' => 27,
            'likes' => ['PHP', 'HTML', 'Development']
        ], Status::OK, [
            'Cache-Control' => 'no-store'
        ]);
    }

    public function testIsCorrectContentType(): void
    {
        self::assertSame('application/json', $this->response->getContentType());
    }

    public function testIsJsonEncodedContent(): void
    {
        self::assertSame(
            '{"user":"Jane","age":27,"likes":["PHP","HTML","Development"]}',
            $this->response->getContent()
        );
    }

    public function testCanReplaceExistingJsonContent(): void
    {
        $this->response->setJson(['error' => 'no-user']);

        self::assertSame('{"error":"no-user"}', $this->response->getContent());
    }

    public function testCanModifyEncodingFlags(): void
    {
        $this->response->setJson([10.0]);

        self::assertSame('[10]', $this->response->getContent());

        $this->response->setEncoding(JSON_PRESERVE_ZERO_FRACTION);

        self::assertSame('[10.0]', $this->response->getContent());
    }
}
