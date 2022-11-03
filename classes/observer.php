<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix;

use core\event;

\defined('MOODLE_INTERNAL') || exit();

/**
 * This hack is required because moodle caches observers, and when they are, our autoloader is not required.
 *
 * This class needs to stay in this location so moodle's own autoloader can kick in.
 *
 * @see https://github.com/moodle/moodle/blob/v3.9.5/lib/classes/event/manager.php#L192-L240
 */
final class observer
{
    /**
     * @see https://github.com/moodle/moodle/blob/02a2e649e92d570c7fa735bf05f69b588036f761/lib/classes/event/manager.php#L222-L230
     */
    public static function observers(): array
    {
        $map = [
            event\course_module_updated::class => [
                self::class,
                'onCourseModuleUpdated',
            ],
            event\course_updated::class => [
                self::class,
                'onCourseUpdated',
            ],
            event\group_created::class => [
                self::class,
                'onGroupCreated',
            ],
            event\group_deleted::class => [
                self::class,
                'onGroupDeleted',
            ],
            event\group_member_added::class => [
                self::class,
                'onGroupMemberAdded',
            ],
            event\group_member_removed::class => [
                self::class,
                'onGroupMemberRemoved',
            ],
            event\group_updated::class => [
                self::class,
                'onGroupUpdated',
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
            event\user_deleted::class => [
                self::class,
                'onUserDeleted',
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
            event\user_updated::class => [
                self::class,
                'onUserUpdated',
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

    public static function onCourseModuleUpdated(event\course_module_updated $event): void
    {
        self::requireAutoloader();

        $other = $event->other;

        if (!\array_key_exists('instanceid', $other)) {
            return;
        }

        $instanceid = $other['instanceid'];

        if (!\is_string($instanceid)) {
            return;
        }

        if (!\array_key_exists('name', $other)) {
            return;
        }

        $name = $other['name'];

        if (!\is_string($name)) {
            return;
        }

        $courseId = Moodle\Domain\CourseId::fromString((string) $event->courseid);
        $moduleId = Plugin\Domain\ModuleId::fromString($instanceid);
        $moduleName = Plugin\Domain\ModuleName::fromString($name);

        self::updateRoomsForModuleFollowingUpdateOfModuleName(
            $courseId,
            $moduleId,
            $moduleName,
        );
    }

    public static function onCourseUpdated(event\course_updated $event): void
    {
        self::requireAutoloader();

        $other = $event->other;

        if (!\array_key_exists('updatedfields', $other)) {
            return;
        }

        $updatedFields = $other['updatedfields'];

        if (!\is_array($updatedFields)) {
            return;
        }

        if (!\array_key_exists('shortname', $updatedFields)) {
            return;
        }

        $shortname = $updatedFields['shortname'];

        if (!\is_string($shortname)) {
            return;
        }

        $courseId = Moodle\Domain\CourseId::fromString((string) $event->courseid);
        $courseShortName = Moodle\Domain\CourseShortName::fromString($shortname);

        self::updateRoomsForCourseFollowingUpdateOfCourseShortName(
            $courseId,
            $courseShortName,
        );
    }

    public static function onGroupCreated(event\group_created $event): void
    {
        self::requireAutoloader();

        $courseId = Moodle\Domain\CourseId::fromString((string) $event->courseid);
        $groupId = Moodle\Domain\GroupId::fromString((string) $event->objectid);

        self::createRoomsForCourseAndGroup(
            $courseId,
            $groupId,
        );
    }

    public static function onGroupDeleted(event\group_deleted $event): void
    {
        self::requireAutoloader();

        $groupId = Moodle\Domain\GroupId::fromString((string) $event->objectid);

        self::removeRoomsForGroup($groupId);
    }

    public static function onGroupMemberAdded(event\group_member_added $event): void
    {
        self::requireAutoloader();

        $courseId = Moodle\Domain\CourseId::fromString((string) $event->courseid);
        $groupId = Moodle\Domain\GroupId::fromString((string) $event->objectid);

        self::synchronizeRoomMembersForAllRoomsOfAllModulesInCourseAndGroup(
            $courseId,
            $groupId,
        );
    }

    public static function onGroupMemberRemoved(event\group_member_removed $event): void
    {
        self::requireAutoloader();

        $courseId = Moodle\Domain\CourseId::fromString((string) $event->courseid);
        $groupId = Moodle\Domain\GroupId::fromString((string) $event->objectid);

        self::synchronizeRoomMembersForAllRoomsOfAllModulesInCourseAndGroup(
            $courseId,
            $groupId,
        );
    }

    public static function onGroupUpdated(event\group_updated $event): void
    {
        self::requireAutoloader();

        $groupId = Moodle\Domain\GroupId::fromString((string) $event->objectid);

        self::updateRoomsForGroup($groupId);
    }

    public static function onRoleAssigned(event\role_assigned $event): void
    {
        self::requireAutoloader();

        self::synchronizeRoomMembersForAllRooms();
    }

    public static function onRoleCapabilitiesUpdated(event\role_capabilities_updated $event): void
    {
        self::requireAutoloader();

        self::synchronizeRoomMembersForAllRooms();
    }

    public static function onRoleDeleted(event\role_deleted $event): void
    {
        self::requireAutoloader();

        self::synchronizeRoomMembersForAllRooms();
    }

    public static function onRoleUnassigned(event\role_unassigned $event): void
    {
        self::requireAutoloader();

        self::synchronizeRoomMembersForAllRooms();
    }

    public static function onUserDeleted(event\user_deleted $event): void
    {
        self::requireAutoloader();

        self::synchronizeRoomMembersForAllRooms();
    }

    public static function onUserEnrolmentCreated(event\user_enrolment_created $event): void
    {
        self::requireAutoloader();

        $courseId = Moodle\Domain\CourseId::fromString((string) $event->courseid);

        self::synchronizeRoomMembersForAllRoomsOfAllModulesInCourse($courseId);
    }

    public static function onUserEnrolmentDeleted(event\user_enrolment_deleted $event): void
    {
        self::requireAutoloader();

        $courseId = Moodle\Domain\CourseId::fromString((string) $event->courseid);

        self::synchronizeRoomMembersForAllRoomsOfAllModulesInCourse($courseId);
    }

    public static function onUserEnrolmentUpdated(event\user_enrolment_updated $event): void
    {
        self::requireAutoloader();

        $courseId = Moodle\Domain\CourseId::fromString((string) $event->courseid);

        self::synchronizeRoomMembersForAllRoomsOfAllModulesInCourse($courseId);
    }

    public static function onUserUpdated(event\user_updated $event): void
    {
        self::requireAutoloader();

        self::synchronizeRoomMembersForAllRooms();
    }

    /**
     * @throws Moodle\Domain\CourseNotFound
     * @throws Moodle\Domain\GroupNotFound
     */
    private static function createRoomsForCourseAndGroup(
        Moodle\Domain\CourseId $courseId,
        Moodle\Domain\GroupId $groupId
    ): void {
        $container = Container::instance();

        $course = $container->moodleCourseRepository()->find($courseId);

        if (!$course instanceof Moodle\Domain\Course) {
            throw Moodle\Domain\CourseNotFound::for($courseId);
        }

        $group = $container->moodleGroupRepository()->find($groupId);

        if (!$group instanceof Moodle\Domain\Group) {
            throw Moodle\Domain\GroupNotFound::for($groupId);
        }

        $modules = $container->moduleRepository()->findAllBy([
            'course' => $courseId->toInt(),
        ]);

        $roomRepository = $container->roomRepository();
        $userRepository = $container->userRepository();
        $roomService = $container->roomService();

        $rooms = \array_reduce(
            $modules,
            static function (array $rooms, Plugin\Domain\Module $module) use ($roomRepository, $roomService, $course, $group): array {
                $room = $roomRepository->findOneBy([
                    'module_id' => $module->id()->toInt(),
                    'group_id' => $group->id()->toInt(),
                ]);

                if (!$room instanceof Plugin\Domain\Room) {
                    $room = $roomService->createRoomForCourseAndGroup(
                        $course,
                        $group,
                        $module,
                    );
                }

                $rooms[] = $room;

                return $rooms;
            },
            [],
        );

        $users = $userRepository->findAllUsersEnrolledInCourseAndGroupWithMatrixUserId(
            $course->id(),
            $group->id(),
        );

        $userIdsOfUsers = Matrix\Domain\UserIdCollection::fromUserIds(...\array_map(static function (Plugin\Domain\User $user): Matrix\Domain\UserId {
            return $user->matrixUserId();
        }, $users));

        $staff = $userRepository->findAllStaffInCourseWithMatrixUserId($course->id());

        $userIdsOfStaff = Matrix\Domain\UserIdCollection::fromUserIds(...\array_map(static function (Plugin\Domain\User $user): Matrix\Domain\UserId {
            return $user->matrixUserId();
        }, $staff));

        $matrixRoomService = $container->matrixRoomService();

        foreach ($rooms as $room) {
            $matrixRoomService->synchronizeRoomMembers(
                $room->matrixRoomId(),
                $userIdsOfUsers,
                $userIdsOfStaff,
            );
        }
    }

    /**
     * @throws Plugin\Domain\ModuleNotFound
     */
    private static function synchronizeRoomMembersForAllRooms(): void
    {
        $container = Container::instance();

        $rooms = $container->roomRepository()->findAll();

        $moduleRepository = $container->moduleRepository();
        $userRepository = $container->userRepository();
        $matrixRoomService = $container->matrixRoomService();

        foreach ($rooms as $room) {
            $module = $moduleRepository->findOneBy([
                'id' => $room->moduleId()->toInt(),
            ]);

            if (!$module instanceof Plugin\Domain\Module) {
                throw Plugin\Domain\ModuleNotFound::for($room->moduleId());
            }

            $groupId = $room->groupId();

            if (!$groupId instanceof Moodle\Domain\GroupId) {
                $groupId = Moodle\Domain\GroupId::fromInt(0);
            } // Moodle wants zero instead of null

            $users = $userRepository->findAllUsersEnrolledInCourseAndGroupWithMatrixUserId(
                $module->courseId(),
                $groupId,
            );

            $staff = $userRepository->findAllStaffInCourseWithMatrixUserId($module->courseId());

            $matrixRoomService->synchronizeRoomMembers(
                $room->matrixRoomId(),
                Matrix\Domain\UserIdCollection::fromUserIds(...\array_map(static function (Plugin\Domain\User $user): Matrix\Domain\UserId {
                    return $user->matrixUserId();
                }, $users)),
                Matrix\Domain\UserIdCollection::fromUserIds(...\array_map(static function (Plugin\Domain\User $user): Matrix\Domain\UserId {
                    return $user->matrixUserId();
                }, $staff)),
            );
        }
    }

    private static function synchronizeRoomMembersForAllRoomsOfAllModulesInCourse(Moodle\Domain\CourseId $courseId): void
    {
        $container = Container::instance();

        $modules = $container->moduleRepository()->findAllBy([
            'course' => $courseId->toInt(),
        ]);

        $userRepository = $container->userRepository();
        $roomRepository = $container->roomRepository();
        $matrixRoomService = $container->matrixRoomService();

        $staff = $userRepository->findAllStaffInCourseWithMatrixUserId($courseId);

        $userIdsOfStaff = Matrix\Domain\UserIdCollection::fromUserIds(...\array_map(static function (Plugin\Domain\User $user): Matrix\Domain\UserId {
            return $user->matrixUserId();
        }, $staff));

        foreach ($modules as $module) {
            $rooms = $roomRepository->findAllBy([
                'module_id' => $module->id()->toInt(),
            ]);

            foreach ($rooms as $room) {
                $groupId = $room->groupId();

                if (!$groupId instanceof Moodle\Domain\GroupId) {
                    $groupId = Moodle\Domain\GroupId::fromInt(0);
                } // Moodle wants zero instead of null

                $users = $userRepository->findAllUsersEnrolledInCourseAndGroupWithMatrixUserId(
                    $courseId,
                    $groupId,
                );

                $matrixRoomService->synchronizeRoomMembers(
                    $room->matrixRoomId(),
                    Matrix\Domain\UserIdCollection::fromUserIds(...\array_map(static function (Plugin\Domain\User $user): Matrix\Domain\UserId {
                        return $user->matrixUserId();
                    }, $users)),
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

        $modules = $container->moduleRepository()->findAllBy([
            'course' => $courseId->toInt(),
        ]);

        $userRepository = $container->userRepository();

        $users = $userRepository->findAllUsersEnrolledInCourseAndGroupWithMatrixUserId(
            $courseId,
            $groupId,
        );

        $userIdsOfUsers = Matrix\Domain\UserIdCollection::fromUserIds(...\array_map(static function (Plugin\Domain\User $user): Matrix\Domain\UserId {
            return $user->matrixUserId();
        }, $users));

        $staff = $userRepository->findAllStaffInCourseWithMatrixUserId($courseId);

        $userIdsOfStaff = Matrix\Domain\UserIdCollection::fromUserIds(...\array_map(static function (Plugin\Domain\User $user): Matrix\Domain\UserId {
            return $user->matrixUserId();
        }, $staff));

        $roomRepository = $container->roomRepository();
        $matrixRoomService = $container->matrixRoomService();

        foreach ($modules as $module) {
            $rooms = $roomRepository->findAllBy([
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

    /**
     * @throws Moodle\Domain\CourseNotFound
     */
    private static function updateRoomsForCourseFollowingUpdateOfCourseShortName(
        Moodle\Domain\CourseId $courseId,
        Moodle\Domain\CourseShortName $courseShortName
    ): void {
        $container = Container::instance();

        $course = $container->moodleCourseRepository()->find($courseId);

        if (!$course instanceof Moodle\Domain\Course) {
            throw Moodle\Domain\CourseNotFound::for($courseId);
        }

        $modules = $container->moduleRepository()->findAllBy([
            'course' => $courseId->toInt(),
        ]);

        $roomRepository = $container->roomRepository();
        $moodleGroupRepository = $container->moodleGroupRepository();
        $nameService = $container->nameService();
        $matrixRoomService = $container->matrixRoomService();

        foreach ($modules as $module) {
            $room = $roomRepository->findOneBy([
                'module_id' => $module->id()->toInt(),
            ]);

            if (!$room instanceof Plugin\Domain\Room) {
                continue;
            }

            $name = $nameService->forCourseAndModule(
                $courseShortName,
                $module->name(),
            );

            $groupId = $room->groupId();

            if ($groupId instanceof Moodle\Domain\GroupId) {
                $group = $moodleGroupRepository->find($groupId);

                if (!$group instanceof Moodle\Domain\Group) {
                    continue;
                }

                $name = $nameService->forGroupCourseAndModule(
                    $group->name(),
                    $courseShortName,
                    $module->name(),
                );
            }

            $matrixRoomService->updateRoom(
                $room->matrixRoomId(),
                $name,
                Matrix\Domain\RoomTopic::fromString($module->topic()->toString()),
            );
        }
    }

    /**
     * @throws Moodle\Domain\CourseNotFound
     * @throws Plugin\Domain\ModuleNotFound
     */
    private static function updateRoomsForModuleFollowingUpdateOfModuleName(
        Moodle\Domain\CourseId $courseId,
        Plugin\Domain\ModuleId $moduleId,
        Plugin\Domain\ModuleName $moduleName
    ): void {
        $container = Container::instance();

        $course = $container->moodleCourseRepository()->find($courseId);

        if (!$course instanceof Moodle\Domain\Course) {
            throw Moodle\Domain\CourseNotFound::for($courseId);
        }

        $module = $container->moduleRepository()->findOneBy([
            'id' => $moduleId->toInt(),
        ]);

        if (!$module instanceof Plugin\Domain\Module) {
            throw Plugin\Domain\ModuleNotFound::for($moduleId);
        }

        $rooms = $container->roomRepository()->findAllBy([
            'module_id' => $moduleId->toInt(),
        ]);

        $nameService = $container->nameService();
        $moodleGroupRepository = $container->moodleGroupRepository();
        $matrixRoomService = $container->matrixRoomService();

        foreach ($rooms as $room) {
            $name = $nameService->forCourseAndModule(
                $course->shortName(),
                $moduleName,
            );

            $groupId = $room->groupId();

            if ($groupId instanceof Moodle\Domain\GroupId) {
                $group = $moodleGroupRepository->find($groupId);

                if (!$group instanceof Moodle\Domain\Group) {
                    continue;
                }

                $name = $nameService->forGroupCourseAndModule(
                    $group->name(),
                    $course->shortName(),
                    $moduleName,
                );
            }

            $matrixRoomService->updateRoom(
                $room->matrixRoomId(),
                $name,
                Matrix\Domain\RoomTopic::fromString($module->topic()->toString()),
            );
        }
    }

    /**
     * @throws Moodle\Domain\GroupNotFound
     */
    private static function updateRoomsForGroup(Moodle\Domain\GroupId $groupId): void
    {
        $container = Container::instance();

        $group = $container->moodleGroupRepository()->find($groupId);

        if (!$group instanceof Moodle\Domain\Group) {
            throw Moodle\Domain\GroupNotFound::for($groupId);
        }

        $moduleRepository = $container->moduleRepository();
        $moodleCourseRepository = $container->moodleCourseRepository();
        $nameService = $container->nameService();
        $matrixRoomService = $container->matrixRoomService();

        $rooms = $container->roomRepository()->findAllBy([
            'group_id' => $groupId->toInt(),
        ]);

        foreach ($rooms as $room) {
            $module = $moduleRepository->findOneBy([
                'id' => $room->moduleId()->toInt(),
            ]);

            if (!$module instanceof Plugin\Domain\Module) {
                continue;
            }

            $course = $moodleCourseRepository->find($module->courseId());

            if (!$course instanceof Moodle\Domain\Course) {
                continue;
            }

            $name = $nameService->forGroupCourseAndModule(
                $group->name(),
                $course->shortName(),
                $module->name(),
            );

            $matrixRoomService->updateRoom(
                $room->matrixRoomId(),
                $name,
                Matrix\Domain\RoomTopic::fromString($module->topic()->toString()),
            );
        }
    }

    /**
     * @throws Moodle\Domain\GroupNotFound
     */
    private static function removeRoomsForGroup(Moodle\Domain\GroupId $groupId): void
    {
        $container = Container::instance();

        $roomRepository = $container->roomRepository();

        $rooms = $roomRepository->findAllBy([
            'group_id' => $groupId->toInt(),
        ]);

        $matrixRoomService = $container->matrixRoomService();

        foreach ($rooms as $room) {
            $matrixRoomService->removeRoom($room->matrixRoomId());

            $roomRepository->remove($room);
        }
    }

    private static function requireAutoloader(): void
    {
        require_once __DIR__ . '/../vendor/autoload.php';
    }
}
