<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Infrastructure;

use core\event;
use mod_matrix\Container;
use mod_matrix\Matrix;
use mod_matrix\Moodle;

\defined('MOODLE_INTERNAL') || exit;

final class EventSubscriber
{
    /**
     * @see https://github.com/moodle/moodle/blob/02a2e649e92d570c7fa735bf05f69b588036f761/lib/classes/event/manager.php#L222-L230
     */
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

        self::prepareRoomsForAllModulesOfCourseAndGroup(
            $courseId,
            $groupId,
        );
    }

    public static function onGroupMemberAdded(event\group_member_added $event): void
    {
        $courseId = Moodle\Domain\CourseId::fromString($event->courseid);
        $groupId = Moodle\Domain\GroupId::fromString($event->objectid);

        self::synchronizeRoomMembersForAllRoomsOfAllModulesInCourseAndGroup(
            $courseId,
            $groupId,
        );
    }

    public static function onGroupMemberRemoved(event\group_member_removed $event): void
    {
        $courseId = Moodle\Domain\CourseId::fromString($event->courseid);
        $groupId = Moodle\Domain\GroupId::fromString($event->objectid);

        self::synchronizeRoomMembersForAllRoomsOfAllModulesInCourseAndGroup(
            $courseId,
            $groupId,
        );
    }

    public static function onRoleAssigned(event\role_assigned $event): void
    {
        self::synchronizeRoomMembersForAllRooms();
    }

    public static function onRoleCapabilitiesUpdated(event\role_capabilities_updated $event): void
    {
        self::synchronizeRoomMembersForAllRooms();
    }

    public static function onRoleDeleted(event\role_deleted $event): void
    {
        self::synchronizeRoomMembersForAllRooms();
    }

    public static function onRoleUnassigned(event\role_unassigned $event): void
    {
        self::synchronizeRoomMembersForAllRooms();
    }

    public static function onUserEnrolmentCreated(event\user_enrolment_created $event): void
    {
        $courseId = Moodle\Domain\CourseId::fromString($event->courseid);

        self::synchronizeRoomMembersForAllRoomsOfAllModulesInCourse($courseId);
    }

    public static function onUserEnrolmentDeleted(event\user_enrolment_deleted $event): void
    {
        $courseId = Moodle\Domain\CourseId::fromString($event->courseid);

        self::synchronizeRoomMembersForAllRoomsOfAllModulesInCourse($courseId);
    }

    public static function onUserEnrolmentUpdated(event\user_enrolment_updated $event): void
    {
        $courseId = Moodle\Domain\CourseId::fromString($event->courseid);

        self::synchronizeRoomMembersForAllRoomsOfAllModulesInCourse($courseId);
    }

    /**
     * @throws Moodle\Domain\GroupNotFound
     */
    private static function prepareRoomsForAllModulesOfCourseAndGroup(
        Moodle\Domain\CourseId $courseId,
        Moodle\Domain\GroupId $groupId
    ): void {
        global $CFG;

        $container = Container::instance();

        $course = $container->moodleCourseRepository()->find($courseId);

        if (!$course instanceof Moodle\Domain\Course) {
            throw new \RuntimeException(\sprintf(
                'Could not find course with id %d.',
                $courseId->toInt(),
            ));
        }

        $group = $container->moodleGroupRepository()->find($groupId);

        if (!$group instanceof Moodle\Domain\Group) {
            throw Moodle\Domain\GroupNotFound::for($groupId);
        }

        $modules = $container->moodleModuleRepository()->findAllBy([
            'course' => $courseId->toInt(),
        ]);

        $moodleRoomRepository = $container->moodleRoomRepository();
        $moodleUserRepository = $container->moodleUserRepository();
        $matrixRoomService = $container->matrixRoomService();

        $moodleNameService = $container->moodleNameService();

        $clock = $container->clock();

        $topic = Matrix\Domain\RoomTopic::fromString(\sprintf(
            '%s/course/view.php?id=%d',
            $CFG->wwwroot,
            $courseId->toInt(),
        ));

        foreach ($modules as $module) {
            $room = $moodleRoomRepository->findOneBy([
                'module_id' => $module->id()->toInt(),
                'group_id' => $group->id()->toInt(),
            ]);

            if (!$room instanceof Moodle\Domain\Room) {
                $name = Matrix\Domain\RoomName::fromString($moodleNameService->createForGroupCourseAndModule(
                    $group->name(),
                    $course->shortName(),
                    $module->name(),
                ));

                $matrixRoomId = $matrixRoomService->createRoom(
                    $name,
                    $topic,
                    [
                        'org.matrix.moodle.course_id' => $course->id()->toInt(),
                        'org.matrix.moodle.group_id' => $group->id()->toInt(),
                    ],
                );

                $room = Moodle\Domain\Room::create(
                    Moodle\Domain\RoomId::unknown(),
                    $module->id(),
                    $group->id(),
                    $matrixRoomId,
                    Moodle\Domain\Timestamp::fromInt($clock->now()->getTimestamp()),
                    Moodle\Domain\Timestamp::fromInt(0),
                );

                $moodleRoomRepository->save($room);
            }

            $users = $moodleUserRepository->findAllUsersEnrolledInCourseAndGroupWithMatrixUserId(
                $course->id(),
                $group->id(),
            );

            $staff = $moodleUserRepository->findAllStaffInCourseWithMatrixUserId($course->id());

            $matrixRoomService->synchronizeRoomMembers(
                $room->matrixRoomId(),
                Matrix\Domain\UserIdCollection::fromUserIds(...\array_map(static function (Moodle\Domain\User $user): Matrix\Domain\UserId {
                    return $user->matrixUserId();
                }, $users)),
                Matrix\Domain\UserIdCollection::fromUserIds(...\array_map(static function (Moodle\Domain\User $user): Matrix\Domain\UserId {
                    return $user->matrixUserId();
                }, $staff)),
            );
        }
    }

    /**
     * @throws \RuntimeException
     */
    private static function synchronizeRoomMembersForAllRooms(): void
    {
        $container = Container::instance();

        $rooms = $container->moodleRoomRepository()->findAll();

        $moodleModuleRepository = $container->moodleModuleRepository();
        $moodleUserRepository = $container->moodleUserRepository();
        $matrixRoomService = $container->matrixRoomService();

        foreach ($rooms as $room) {
            $module = $moodleModuleRepository->findOneBy([
                'id' => $room->moduleId()->toInt(),
            ]);

            if (!$module instanceof Moodle\Domain\Module) {
                throw new \RuntimeException(\sprintf(
                    'Module with id "%d" was not found.',
                    $room->moduleId()->toInt(),
                ));
            }

            $groupId = $room->groupId();

            if (!$groupId instanceof Moodle\Domain\GroupId) {
                $groupId = Moodle\Domain\GroupId::fromInt(0);
            } // Moodle wants zero instead of null

            $users = $moodleUserRepository->findAllUsersEnrolledInCourseAndGroupWithMatrixUserId(
                $module->courseId(),
                $groupId,
            );

            $userIdsOfUsers = Matrix\Domain\UserIdCollection::fromUserIds(...\array_map(static function (Moodle\Domain\User $user): Matrix\Domain\UserId {
                return $user->matrixUserId();
            }, $users));

            $staff = $moodleUserRepository->findAllStaffInCourseWithMatrixUserId($module->courseId());

            $userIdsOfStaff = Matrix\Domain\UserIdCollection::fromUserIds(...\array_map(static function (Moodle\Domain\User $user): Matrix\Domain\UserId {
                return $user->matrixUserId();
            }, $staff));

            $matrixRoomService->synchronizeRoomMembers(
                $room->matrixRoomId(),
                $userIdsOfUsers,
                $userIdsOfStaff,
            );
        }
    }

    private static function synchronizeRoomMembersForAllRoomsOfAllModulesInCourse(Moodle\Domain\CourseId $courseId): void
    {
        $container = Container::instance();

        $modules = $container->moodleModuleRepository()->findAllBy([
            'course' => $courseId->toInt(),
        ]);

        $moodleUserRepository = $container->moodleUserRepository();
        $moodleRoomRepository = $container->moodleRoomRepository();
        $matrixRoomService = $container->matrixRoomService();

        $staff = $moodleUserRepository->findAllStaffInCourseWithMatrixUserId($courseId);

        $userIdsOfStaff = Matrix\Domain\UserIdCollection::fromUserIds(...\array_map(static function (Moodle\Domain\User $user): Matrix\Domain\UserId {
            return $user->matrixUserId();
        }, $staff));

        foreach ($modules as $module) {
            $rooms = $moodleRoomRepository->findAllBy([
                'module_id' => $module->id()->toInt(),
            ]);

            foreach ($rooms as $room) {
                $groupId = $room->groupId();

                if (!$groupId instanceof Moodle\Domain\GroupId) {
                    $groupId = Moodle\Domain\GroupId::fromInt(0);
                } // Moodle wants zero instead of null

                $users = $moodleUserRepository->findAllUsersEnrolledInCourseAndGroupWithMatrixUserId(
                    $courseId,
                    $groupId,
                );

                $userIdsOfUsers = Matrix\Domain\UserIdCollection::fromUserIds(...\array_map(static function (Moodle\Domain\User $user): Matrix\Domain\UserId {
                    return $user->matrixUserId();
                }, $users));

                $matrixRoomService->synchronizeRoomMembers(
                    $room->matrixRoomId(),
                    $userIdsOfUsers,
                    $userIdsOfStaff,
                );
            }
        }
    }

    private static function synchronizeRoomMembersForAllRoomsOfAllModulesInCourseAndGroup(
        Moodle\Domain\CourseId $courseId,
        Moodle\Domain\GroupId $groupId
    ): void {
        $container = Container::instance();

        $modules = $container->moodleModuleRepository()->findAllBy([
            'course' => $courseId->toInt(),
        ]);

        $moodleUserRepository = $container->moodleUserRepository();

        $users = $moodleUserRepository->findAllUsersEnrolledInCourseAndGroupWithMatrixUserId(
            $courseId,
            $groupId,
        );

        $userIdsOfUsers = Matrix\Domain\UserIdCollection::fromUserIds(...\array_map(static function (Moodle\Domain\User $user): Matrix\Domain\UserId {
            return $user->matrixUserId();
        }, $users));

        $staff = $moodleUserRepository->findAllStaffInCourseWithMatrixUserId($courseId);

        $userIdsOfStaff = Matrix\Domain\UserIdCollection::fromUserIds(...\array_map(static function (Moodle\Domain\User $user): Matrix\Domain\UserId {
            return $user->matrixUserId();
        }, $staff));

        $moodleRoomRepository = $container->moodleRoomRepository();
        $matrixRoomService = $container->matrixRoomService();

        foreach ($modules as $module) {
            $rooms = $moodleRoomRepository->findAllBy([
                'group_id' => $groupId->toInt(),
                'module_id' => $module->id()->toInt(),
            ]);

            foreach ($rooms as $room) {
                $matrixRoomService->synchronizeRoomMembers(
                    $room->matrixRoomId(),
                    $userIdsOfUsers,
                    $userIdsOfStaff,
                );
            }
        }
    }
}
