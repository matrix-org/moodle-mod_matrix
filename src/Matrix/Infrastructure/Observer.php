<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Matrix\Infrastructure;

use core\event;
use mod_matrix\Container;
use mod_matrix\Moodle;

\defined('MOODLE_INTERNAL') || exit;

class Observer
{
    public static function onGroupMemberChange($event): void
    {
        $courseId = Moodle\Domain\CourseId::fromString($event->courseid);
        $groupId = Moodle\Domain\GroupId::fromString($event->objectid);

        $container = Container::instance();

        $moduleRepository = $container->moduleRepository();

        $modules = $moduleRepository->findAllBy([
            'course' => $courseId->toInt(),
        ]);

        $service = $container->service();

        foreach ($modules as $module) {
            $service->synchronizeRoomMembersForAllRoomsOfModuleAndGroup(
                $module->id(),
                $groupId,
            );
        }
    }

    public static function onGroupCreated(event\group_created $event): void
    {
        $courseId = Moodle\Domain\CourseId::fromString($event->courseid);

        $container = Container::instance();

        $moduleRepository = $container->moduleRepository();

        $modules = $moduleRepository->findAllBy([
            'course' => $courseId->toInt(),
        ]);

        $service = $container->service();

        foreach ($modules as $module) {
            $service->prepareRoomForModuleAndGroup(
                $module,
                Moodle\Domain\GroupId::fromString($event->objectid),
            );
        }
    }

    public static function onRoleChanged(): void
    {
        $service = Container::instance()->service();

        $service->synchronizeRoomMembersForAllRooms();
    }

    public static function onUserEnrolmentChanged($event): void
    {
        $courseId = Moodle\Domain\CourseId::fromString($event->courseid);

        $container = Container::instance();

        $moduleRepository = $container->moduleRepository();

        $modules = $moduleRepository->findAllBy([
            'course' => $courseId->toInt(),
        ]);

        $service = $container->service();

        foreach ($modules as $module) {
            $service->synchronizeRoomMembersForAllRoomsOfModule($module->id());
        }
    }
}
