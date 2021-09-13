<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix;

use core\event;

defined('MOODLE_INTERNAL') || exit;

class Observer
{
    public static function onGroupMemberChange($event): void
    {
        $courseId = Moodle\Domain\CourseId::fromString($event->courseid);

        $container = Container::instance();

        $moduleRepository = $container->moduleRepository();

        $modules = $moduleRepository->findAllBy([
            'course' => $courseId->toInt(),
        ]);

        if ([] === $modules) {
            return;
        }

        $service = $container->service();

        $service->synchronizeRoomMembersForCourseAndGroup(
            $courseId,
            Moodle\Domain\GroupId::fromString($event->objectid)
        );
    }

    public static function onGroupCreated(event\group_created $event): void
    {
        $courseId = Moodle\Domain\CourseId::fromString($event->courseid);

        $container = Container::instance();

        $moduleRepository = $container->moduleRepository();

        $modules = $moduleRepository->findAllBy([
            'course' => $courseId->toInt(),
        ]);

        if ([] === $modules) {
            return;
        }

        $service = $container->service();

        $service->prepareRoomForCourseAndGroup(
            $courseId,
            Moodle\Domain\GroupId::fromString($event->objectid)
        );
    }

    public static function onRoleChanged(): void
    {
        $service = Container::instance()->service();

        $service->synchronizeAll(null); // ALL the rooms
    }

    public static function onUserEnrolmentChanged($event): void
    {
        $courseId = Moodle\Domain\CourseId::fromString($event->courseid);

        $service = Container::instance()->service();

        $service->synchronizeAll($courseId);
    }
}
