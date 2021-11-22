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

final class EventSubscriber
{
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

    public static function onGroupMemberAdded(event\group_member_added $event): void
    {
        self::onGroupMemberChange($event);
    }

    public static function onGroupMemberRemoved(event\group_member_removed $event): void
    {
        self::onGroupMemberChange($event);
    }

    public static function onRoleAssigned(event\role_assigned $event): void
    {
        self::onRoleChanged();
    }

    public static function onRoleCapabilitiesUpdated(event\role_capabilities_updated $event): void
    {
        self::onRoleChanged();
    }

    public static function onRoleDeleted(event\role_deleted $event): void
    {
        self::onRoleChanged();
    }

    public static function onRoleUnassigned(event\role_unassigned $event): void
    {
        self::onRoleChanged();
    }

    public static function onUserEnrolmentCreated(event\user_enrolment_created $event): void
    {
        self::onUserEnrolmentChanged($event);
    }

    public static function onUserEnrolmentDeleted(event\user_enrolment_deleted $event): void
    {
        self::onUserEnrolmentChanged($event);
    }

    public static function onUserEnrolmentUpdated(event\user_enrolment_updated $event): void
    {
        self::onUserEnrolmentChanged($event);
    }

    private static function onGroupMemberChange($event): void
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

    private static function onRoleChanged(): void
    {
        $service = Container::instance()->service();

        $service->synchronizeRoomMembersForAllRooms();
    }

    private static function onUserEnrolmentChanged($event): void
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
