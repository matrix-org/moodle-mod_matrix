<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Matrix\Domain;

/**
 * @psalm-immutable
 */
final class Homeserver
{
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
        if (1 !== \preg_match('/^(?P<homeserver>\S+(\.\S+)+)$/', $value)) {
            throw new \InvalidArgumentException(\sprintf(
                'Value "%s" does not appear to be a valid Matrix homeserver.',
                $value,
            ));
        }

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
