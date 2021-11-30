<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
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
