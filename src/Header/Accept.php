<?php declare(strict_types=1);

namespace Swiftly\Http\Header;

use Swiftly\Http\Exception\HeaderException;
use Swiftly\Http\HeaderLineInterface;
use Swiftly\Http\Response\Response;

use function array_change_key_case;
use function array_shift;
use function explode;
use function str_replace;
use function strtolower;
use function implode;

use const CASE_LOWER;

/**
 * @upgrade:php8.1 Add readonly attribute
 * @upgrade:php8.3 Add #[Override] attribute
 *
 * @readonly
 * @psalm-immutable
 */
final class Accept implements HeaderLineInterface
{
    public const NAME = 'Accept';

    /** @var array<string, true|string[]> */
    private array $values = [];

    /**
     * @param array<string, true|string[]> $values
     */
    public function __construct(
        array $values = [],
    ) {
        $this->values = array_change_key_case($values, CASE_LOWER);
    }

    /**
     * {@inheritDoc}
     */
    public static function name(): string
    {
        return self::NAME;
    }

    /**
     * {@inheritDoc}
     *
     * @pure
     */
    public static function fromValue(string $value): static
    {
        $value = strtolower($value);
        $value = str_replace(' ', '', $value);

        $accepts = [];

        foreach (explode(',', $value) as $mimeType) {
            $components = explode(';', $mimeType);

            $type = array_shift($components);

            $accepts[$type] = $components ?: true;
        }

        return new self($accepts);
    }

    /**
     * {@inheritDoc}
     *
     * @upgrade:php8.1 Use never return type
     */
    public function applyTo(Response $response): void
    {
        throw HeaderException::forRequestOnlyHeader(self::NAME);
    }

    /**
     * Determine if the given mime type would satisfy the `Accept` header.
     */
    public function allows(string $mimeType): bool
    {
        if (empty($this->values) || isset($this->values['*/*'])) {
            return true;
        }

        $mimeType = strtolower($mimeType);

        if (isset($this->values[$mimeType])) {
            return true;
        }

        [$type] = explode('/', $mimeType, 2);

        return isset($this->values["{$type}/*"]);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        $output = [];

        foreach ($this->values as $mimeType => $components) {
            if (true === $components || empty($components)) {
                $output[] = $mimeType;

                continue;
            }

            $output[] = "{$mimeType};" . implode(';', $components);
        }

        return implode(',', $output);
    }
}
