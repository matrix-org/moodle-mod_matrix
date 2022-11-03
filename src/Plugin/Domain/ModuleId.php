<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Plugin\Domain;

/**
 * @psalm-immutable
 */
final class ModuleId
{
    private $value;

    private function __construct(int $value)
    {
        $this->value = $value;
    }

    public static function unknown(): self
    {
        return new self(-1);
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

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
