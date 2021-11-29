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
    public function find(GroupId $groupId): ?object
    {
        $group = groups_get_group($groupId->toInt());

        if (!\is_object($group)) {
            return null;
        }

        return $group;
    }
}
