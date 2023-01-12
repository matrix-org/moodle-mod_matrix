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
final class UserId
{
    private $username;
    private $homeserver;

    private function __construct(
        Username $username,
        Homeserver $homeserver
    ) {
        $this->username = $username;
        $this->homeserver = $homeserver;
    }

    public static function create(
        Username $username,
        Homeserver $homeserver
    ): self {
        return new self(
            $username,
            $homeserver,
        );
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function fromString(string $value): self
    {
        if (1 !== \preg_match('/^@(?P<username>[\da-z0-9_\-\/=.]+):(?P<homeserver>\S+(\.\S+)+)$/', $value, $matches)) {
            throw new \InvalidArgumentException(\sprintf(
                'Value "%s" does not appear to be a valid Matrix user identifier.',
                $value,
            ));
        }

        return new self(
            Username::fromString($matches['username']),
            Homeserver::fromString($matches['homeserver']),
        );
    }

    public function username(): Username
    {
        return $this->username;
    }

    public function homeserver(): Homeserver
    {
        return $this->homeserver;
    }

    public function toString(): string
    {
        return \sprintf(
            '@%s:%s',
            $this->username->toString(),
            $this->homeserver->toString(),
        );
    }

    public function equals(self $other): bool
    {
        return $this->toString() === $other->toString();
    }
}
