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
        parent::__construct(
            $status,
            // Not required but good practice
            "Redirecting to: {$location}",
            $headers
        );
        
        $this->location = $location;
        $this->headers->set('Location', $location);
        $this->setContentType('text/plain');
    }
}
