<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Infrastructure;

use mod_matrix\Moodle;
use mod_matrix\Moodle\Domain\GroupId;

final class MoodleFunctionBasedGroupRepository implements Moodle\Domain\GroupRepository
{
    public function find(GroupId $groupId): ?Moodle\Domain\Group
    {
        $group = groups_get_group($groupId->toInt());

        if (!\is_object($group)) {
            return null;
        }

        if (!isset($group->name)) {
            throw new \RuntimeException('Expected object to have a name property, but it does not.');
        }

        if (!\is_string($group->name)) {
            throw new \RuntimeException(\sprintf(
                'Expected name property to be a string, got %s instead.',
                \is_object($group->name) ? \get_class($group->name) : \gettype($group->name),
            ));
        }

        return Moodle\Domain\Group::create(
            $groupId,
            Moodle\Domain\GroupName::fromString($group->name),
        );
    }
}
