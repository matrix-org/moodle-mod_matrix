<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Plugin\Infrastructure;

use mod_matrix\Matrix;
use mod_matrix\Moodle;
use mod_matrix\Plugin;

final class RoomNormalizer
{
    public function denormalize(object $normalized): Plugin\Domain\Room
    {
        $groupId = null;

        if (null !== $normalized->group_id) {
            $groupId = Moodle\Domain\GroupId::fromString((string) $normalized->group_id);
        }

        return Plugin\Domain\Room::create(
            Plugin\Domain\RoomId::fromString((string) $normalized->id),
            Plugin\Domain\ModuleId::fromString((string) $normalized->module_id),
            $groupId,
            Matrix\Domain\RoomId::fromString($normalized->room_id),
            Moodle\Domain\Timestamp::fromString((string) $normalized->timecreated),
            Moodle\Domain\Timestamp::fromString((string) $normalized->timemodified),
        );
    }

    public function normalize(Plugin\Domain\Room $denormalized): object
    {
        $groupId = null;

        if (null !== $denormalized->groupId()) {
            $groupId = $denormalized->groupId()->toInt();
        }

        return (object) [
            'id' => $denormalized->id()->toInt(),
            'module_id' => $denormalized->moduleId()->toInt(),
            'group_id' => $groupId,
            'room_id' => $denormalized->matrixRoomId()->toString(),
            'timecreated' => $denormalized->timecreated()->toInt(),
            'timemodified' => $denormalized->timemodified()->toInt(),
        ];
    }
}
