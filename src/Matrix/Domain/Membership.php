<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Matrix\Domain;

/**
 * @psalm-immutable
 */
final class Membership
{
    private $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function ban(): self
    {
        return new self('ban');
    }

    public static function invite(): self
    {
        return new self('invite');
    }

    public static function join(): self
    {
        return new self('join');
    }

    public static function leave(): self
    {
        return new self('leave');
    }

    public function toString(): string
    {
        return $this->value;
    }
}
