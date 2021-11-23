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
    public static function observers(): array
    {
        $map = [
            event\group_created::class => [
                self::class,
                'onGroupCreated',
            ],
            event\group_member_added::class => [
                self::class,
                'onGroupMemberAdded',
            ],
            event\group_member_removed::class => [
                self::class,
                'onGroupMemberRemoved',
            ],
            event\role_assigned::class => [
                self::class,
                'onRoleAssigned',
            ],
            event\role_capabilities_updated::class => [
                self::class,
                'onRoleCapabilitiesUpdated',
            ],
            event\role_deleted::class => [
                self::class,
                'onRoleDeleted',
            ],
            event\role_unassigned::class => [
                self::class,
                'onRoleUnassigned',
            ],
            event\user_enrolment_created::class => [
                self::class,
                'onUserEnrolmentCreated',
            ],
            event\user_enrolment_deleted::class => [
                self::class,
                'onUserEnrolmentDeleted',
            ],
            event\user_enrolment_updated::class => [
                self::class,
                'onUserEnrolmentUpdated',
            ],
        ];

        return \array_map(static function (string $event, array $callback): array {
            return [
                'callback' => $callback,
                'eventname' => $event,
                'internal' => false,
            ];
        }, \array_keys($map), \array_values($map));
    }

    public static function onGroupCreated(event\group_created $event): void
    {
        $courseId = Moodle\Domain\CourseId::fromString($event->courseid);
        $groupId = Moodle\Domain\GroupId::fromString($event->objectid);

        $service = Container::instance()->service();

        $service->prepareRoomsForAllModulesOfCourseAndGroup(
            $courseId,
            $groupId,
        );
    }

    public static function onGroupMemberAdded(event\group_member_added $event): void
    {
        $courseId = Moodle\Domain\CourseId::fromString($event->courseid);
        $groupId = Moodle\Domain\GroupId::fromString($event->objectid);

        $service = Container::instance()->service();

        $service->synchronizeRoomMembersForAllRoomsOfAllModulesInCourseAndGroup(
            $courseId,
            $groupId,
        );
    }

    public static function onGroupMemberRemoved(event\group_member_removed $event): void
    {
        $courseId = Moodle\Domain\CourseId::fromString($event->courseid);
        $groupId = Moodle\Domain\GroupId::fromString($event->objectid);

        $service = Container::instance()->service();

        $service->synchronizeRoomMembersForAllRoomsOfAllModulesInCourseAndGroup(
            $courseId,
            $groupId,
        );
    }

    public static function onRoleAssigned(event\role_assigned $event): void
    {
        $service = Container::instance()->service();

        $service->synchronizeRoomMembersForAllRooms();
    }

    public static function onRoleCapabilitiesUpdated(event\role_capabilities_updated $event): void
    {
        $service = Container::instance()->service();

        $service->synchronizeRoomMembersForAllRooms();
    }

    public static function onRoleDeleted(event\role_deleted $event): void
    {
        $service = Container::instance()->service();

        $service->synchronizeRoomMembersForAllRooms();
    }

    public static function onRoleUnassigned(event\role_unassigned $event): void
    {
        $service = Container::instance()->service();

        $service->synchronizeRoomMembersForAllRooms();
    }

    public static function onUserEnrolmentCreated(event\user_enrolment_created $event): void
    {
        $courseId = Moodle\Domain\CourseId::fromString($event->courseid);

        $service = Container::instance()->service();

        $service->synchronizeRoomMembersForAllRoomsOfAllModulesInCourse($courseId);
    }

    public static function onUserEnrolmentDeleted(event\user_enrolment_deleted $event): void
    {
        $courseId = Moodle\Domain\CourseId::fromString($event->courseid);

        $service = Container::instance()->service();

        $service->synchronizeRoomMembersForAllRoomsOfAllModulesInCourse($courseId);
    }

    public static function onUserEnrolmentUpdated(event\user_enrolment_updated $event): void
    {
        $courseId = Moodle\Domain\CourseId::fromString($event->courseid);

        $service = Container::instance()->service();

        $service->synchronizeRoomMembersForAllRoomsOfAllModulesInCourse($courseId);
    }
}
