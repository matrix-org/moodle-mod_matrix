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
final class UserIdCollection
{
    /**
     * @var array<int, UserId>
     */
    private $userIds;

    private function __construct(UserId ...$userIds)
    {
        $this->userIds = \array_values($userIds);
    }

    public static function fromUserIds(UserId ...$userIds): self
    {
        return new self(...$userIds);
    }

    public function diff(self $other): self
    {
        return new self(...\array_filter($this->userIds, static function (UserId $userId) use ($other): bool {
            return !\in_array(
                $userId,
                $other->toArray(),
                false,
            );
        }));
    }

    public function filter(\Closure $closure): self
    {
        return new self(...\array_filter($this->userIds, static function (UserId $userId) use ($closure): bool {
            return $closure($userId) === true;
        }));
    }

    public function merge(self $other): self
    {
        return new self(...\array_merge(
            $this->userIds,
            $other->userIds,
        ));
    }

    /**
     * @return array<int, UserId>
     */
    public function toArray(): array
    {
        return $this->userIds;
    }
}
