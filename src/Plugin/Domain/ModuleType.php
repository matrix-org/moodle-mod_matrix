<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Plugin\Domain;

/**
 * @psalm-immutable
 */
final class ModuleType
{
    private $value;

    private function __construct(int $value)
    {
        $this->value = $value;
    }

    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    public static function fromString(string $value): self
    {
        return new self((int) $value);
    }

    public function toInt(): int
    {
        return $this->value;
    }
}
