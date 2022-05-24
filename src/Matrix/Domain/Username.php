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
final class Username
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
        if (1 !== \preg_match('/^(?P<username>[\da-z0-9_-]+)$/', $value)) {
            throw new \InvalidArgumentException(\sprintf(
                'Value "%s" does not appear to be a valid Matrix username.',
                $value,
            ));
        }

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
