<?php declare(strict_types=1);

namespace Swiftly\Http\Response;

use Swiftly\Http\Response\Response;
use Swiftly\Http\Status;

use function json_encode;

use const JSON_HEX_AMP;
use const JSON_HEX_TAG;
use const JSON_HEX_APOS;
use const JSON_HEX_QUOT;

/**
 * Class used to send JSON responses to the client
 *
 * @api
 */
class JsonResponse extends Response
{
    /**
     * Array representation of JSON content
     *
     * @var array $json JSON content
     */
    protected array $json;

    /**
     * Encoding options used during json_encode
     *
     * @var int $encoding Encoding options
     */
    protected int $encoding = JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT;

    /**
     * Creates a new JSON HTTP response using the values provided
     *
     * @psalm-param Status::* $status
     *
     * @param array $json                             Response content
     * @param int $status                             Status code
     * @param array<non-empty-string,string> $headers HTTP header values
     */
    public function __construct(
        array $json = [],
        int $status = Status::OK,
        array $headers = []
    ) {
        parent::__construct(
            $status,
            json_encode($json, $this->encoding),
            $headers
        );

        $this->json = $json;
        $this->setContentType('application/json');
    }

    /**
     * Sets the JSON content of this response
     *
     * @param array $json Response content
     */
    public function setJson(array $json): void
    {
        $this->json = $json;
        $this->content = json_encode($json, $this->encoding);
    }

    /**
     * Sets the encoding options used during json_encode
     *
     * Re-encodes any existing JSON content that has been set.
     *
     * @param int $encoding Encoding options
     */
    public function setEncoding(int $encoding): void
    {
        $this->encoding = $encoding;
        $this->content = json_encode($this->json, $encoding);
    }
}
