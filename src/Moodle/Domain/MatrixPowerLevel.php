<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Domain;

final class MatrixPowerLevel
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
