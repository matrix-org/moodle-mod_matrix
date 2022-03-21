<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Plugin\Domain;

final class ModuleTarget
{
    private const VALUE_ELEMENT_URL = 'element-url';
    private const VALUE_MATRIX_TO = 'matrix-to';
    private $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function fromString(string $value): self
    {
        $values = [
            self::VALUE_ELEMENT_URL,
            self::VALUE_MATRIX_TO,
        ];

        if (!\in_array($value, $values, true)) {
            throw new \InvalidArgumentException(\sprintf(
                'Value needs to be one of "%s", got "%s" instead.',
                \implode('", "', $values),
                $value,
            ));
        }

        return new self($value);
    }

    public static function elementUrl(): self
    {
        return new self(self::VALUE_ELEMENT_URL);
    }

    public static function matrixTo(): self
    {
        return new self(self::VALUE_MATRIX_TO);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
