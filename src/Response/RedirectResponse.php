<?php

namespace Swiftly\Http\Response;

use Swiftly\Http\Response\Response;
use Swiftly\Http\Status;

/**
 * Class used to redirect the client to a new resource
 *
 * @api
 */
class RedirectResponse extends Response
{
    /**
     * Location the client is to be redirected to
     *
     * @readonly
     * @var non-empty-string $location
     */
    protected string $location;

    /**
     * Creates a new redirect toward the given location
     *
     * @psalm-param Status::* $status
     *
     * @param non-empty-string $location              Redirect destination
     * @param int $status                             Status code
     * @param array<non-empty-string,string> $headers HTTP header values
     */
    public function __construct(
        string $location,
        int $status = Status::SEE_OTHER,
        array $headers = []
    ) {
        $this->location = $location;

        parent::__construct('', $status, $headers);
    }

    /** {@inheritdoc} */
    public function send(): void
    {
        $this->headers->set("Location", $this->location);

        // Not required, but good practice
        if (empty($this->content)) {
            $this->content = "Redirecting to: {$this->location}";
        }

        parent::send();
    }
}
