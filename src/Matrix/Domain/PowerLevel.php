<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Matrix\Domain;

final class PowerLevel
{
    private $value;

    private function __construct(int $value)
    {
        $this->value = $value;
    }

    public static function bot(): self
    {
        return new self(100);
    }

    public static function staff(): self
    {
        return new self(99);
    }

    public static function redactor(): self
    {
        return new self(50);
    }

    public static function default(): self
    {
        return new self(0);
    }

    public function toInt(): int
    {
        return $this->value;
    }
}
