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
final class ModuleName
{
    public const LENGTH_MAX = 255;
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
        if (self::LENGTH_MAX < \mb_strlen($value)) {
            throw new \InvalidArgumentException(\sprintf(
                'Value "%s" is longer than %d characters.',
                $value,
                self::LENGTH_MAX,
            ));
        }

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
