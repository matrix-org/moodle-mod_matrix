<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Matrix\Application;

use Ergebnis\Clock;
use mod_matrix\Matrix;
use mod_matrix\Moodle;

final class MatrixService
{
    private $api;
    private $configuration;
    private $courseRepository;
    private $groupRepository;
    private $moduleRepository;
    private $roomRepository;
    private $userRepository;
    private $clock;

    public function __construct(
        Matrix\Application\Api $api,
        Matrix\Application\Configuration $configuration,
        Moodle\Domain\CourseRepository $courseRepository,
        Moodle\Domain\GroupRepository $groupRepository,
        Moodle\Domain\ModuleRepository $moduleRepository,
        Moodle\Domain\RoomRepository $roomRepository,
        Moodle\Domain\UserRepository $userRepository,
        Clock\Clock $clock
    ) {
        $this->api = $api;
        $this->configuration = $configuration;
        $this->courseRepository = $courseRepository;
        $this->groupRepository = $groupRepository;
        $this->moduleRepository = $moduleRepository;
        $this->roomRepository = $roomRepository;
        $this->userRepository = $userRepository;
        $this->clock = $clock;
    }

    public function urlForRoom(Matrix\Domain\RoomId $roomId): string
    {
        if ('' !== $this->configuration->elementUrl()) {
            return \sprintf(
                '%s/#/room/%s',
                $this->configuration->elementUrl(),
                $roomId->toString(),
            );
        }

        return \sprintf(
            'https://matrix.to/#/%s',
            $roomId->toString(),
        );
    }

    public function prepareRoomsForAllModulesOfCourseAndGroup(
        Moodle\Domain\CourseId $courseId,
        Moodle\Domain\GroupId $groupId
    ): void {
        global $CFG;

        $modules = $this->moduleRepository->findAllBy([
            'course' => $courseId->toInt(),
        ]);

        foreach ($modules as $module) {
            $this->prepareRoomForModuleAndGroup(
                Matrix\Domain\RoomTopic::fromString(\sprintf(
                    '%s/course/view.php?id=%d',
                    $CFG->wwwroot,
                    $module->courseId()->toInt(),
                )),
                $module,
                $groupId,
            );
        }
    }

    public function prepareRoomForModuleAndGroup(
        Matrix\Domain\RoomTopic $topic,
        Moodle\Domain\Module $module,
        ?Moodle\Domain\GroupId $groupId
    ): void {
        $course = $this->courseRepository->find($module->courseId());

        if (!$course instanceof Moodle\Domain\Course) {
            throw new \RuntimeException(\sprintf(
                'Could not find course with id %d.',
                $module->courseId()->toInt(),
            ));
        }

        $whoami = $this->api->whoAmI();

        $botPowerLevel = Matrix\Domain\PowerLevel::bot();
        $staffPowerLevel = Matrix\Domain\PowerLevel::staff();
        $redactorPowerLevel = Matrix\Domain\PowerLevel::redactor();

        $roomOptions = [
            'creation_content' => [
                'org.matrix.moodle.course_id' => $module->courseId()->toInt(),
            ],
            'initial_state' => [
                [
                    'content' => [
                        'guest_access' => 'forbidden',
                    ],
                    'state_key' => '',
                    'type' => 'm.room.guest_access',
                ],
            ],
            'name' => \sprintf(
                '%s (%s)',
                $course->name()->toString(),
                $module->name()->toString(),
            ),
            'power_level_content_override' => [
                'ban' => $botPowerLevel->toInt(),
                'invite' => $botPowerLevel->toInt(),
                'kick' => $botPowerLevel->toInt(),
                'events' => [
                    'm.room.name' => $botPowerLevel->toInt(),
                    'm.room.power_levels' => $botPowerLevel->toInt(),
                    'm.room.history_visibility' => $staffPowerLevel->toInt(),
                    'm.room.canonical_alias' => $staffPowerLevel->toInt(),
                    'm.room.avatar' => $staffPowerLevel->toInt(),
                    'm.room.tombstone' => $botPowerLevel->toInt(),
                    'm.room.server_acl' => $botPowerLevel->toInt(),
                    'm.room.encryption' => $botPowerLevel->toInt(),
                    'm.room.join_rules' => $botPowerLevel->toInt(),
                    'm.room.guest_access' => $botPowerLevel->toInt(),
                ],
                'events_default' => 0,
                'state_default' => $staffPowerLevel->toInt(),
                'redact' => $redactorPowerLevel->toInt(),
                'users' => [
                    $whoami->toString() => $botPowerLevel->toInt(),
                ],
            ],
            'preset' => 'private_chat',
            'topic' => $topic->toString(),
        ];

        if ($groupId instanceof Moodle\Domain\GroupId) {
            $group = $this->groupRepository->find($groupId);

            if (!$group instanceof Moodle\Domain\Group) {
                throw new \RuntimeException(\sprintf(
                    'Could not find group with id %d.',
                    $groupId->toInt(),
                ));
            }

            $roomForModuleAndGroup = $this->roomRepository->findOneBy([
                'module_id' => $module->id()->toInt(),
                'group_id' => $groupId->toInt(),
            ]);

            if (!$roomForModuleAndGroup instanceof Moodle\Domain\Room) {
                $roomOptions['name'] = \sprintf(
                    '%s: %s (%s)',
                    $group->name()->toString(),
                    $course->name()->toString(),
                    $module->name()->toString(),
                );
                $roomOptions['creation_content']['org.matrix.moodle.group_id'] = $groupId->toInt();

                $matrixRoomId = $this->api->createRoom($roomOptions);

                $roomForModuleAndGroup = Moodle\Domain\Room::create(
                    Moodle\Domain\RoomId::unknown(),
                    $module->id(),
                    $groupId,
                    $matrixRoomId,
                    Moodle\Domain\Timestamp::fromInt($this->clock->now()->getTimestamp()),
                    Moodle\Domain\Timestamp::fromInt(0),
                );

                $this->roomRepository->save($roomForModuleAndGroup);
            }

            $this->synchronizeRoomMembersForRoom($roomForModuleAndGroup);

            return;
        }

        $roomForModule = $this->roomRepository->findOneBy([
            'module_id' => $module->id()->toInt(),
            'group_id' => null,
        ]);

        if (!$roomForModule instanceof Moodle\Domain\Room) {
            $matrixRoomId = $this->api->createRoom($roomOptions);

            $roomForModule = Moodle\Domain\Room::create(
                Moodle\Domain\RoomId::unknown(),
                $module->id(),
                null,
                $matrixRoomId,
                Moodle\Domain\Timestamp::fromInt($this->clock->now()->getTimestamp()),
                Moodle\Domain\Timestamp::fromInt(0),
            );

            $this->roomRepository->save($roomForModule);
        }

        $this->synchronizeRoomMembersForRoom($roomForModule);
    }

    public function synchronizeRoomMembersForAllRooms(): void
    {
        $rooms = $this->roomRepository->findAll();

        foreach ($rooms as $room) {
            $this->synchronizeRoomMembersForRoom($room);
        }
    }

    public function synchronizeRoomMembersForAllRoomsOfAllModulesInCourse(Moodle\Domain\CourseId $courseId): void
    {
        $modules = $this->moduleRepository->findAllBy([
            'course' => $courseId->toInt(),
        ]);

        foreach ($modules as $module) {
            $rooms = $this->roomRepository->findAllBy([
                'module_id' => $module->id()->toInt(),
            ]);

            foreach ($rooms as $room) {
                $this->synchronizeRoomMembersForRoom($room);
            }
        }
    }

    public function synchronizeRoomMembersForAllRoomsOfAllModulesInCourseAndGroup(
        Moodle\Domain\CourseId $courseId,
        Moodle\Domain\GroupId $groupId
    ): void {
        $modules = $this->moduleRepository->findAllBy([
            'course' => $courseId->toInt(),
        ]);

        foreach ($modules as $module) {
            $rooms = $this->roomRepository->findAllBy([
                'group_id' => $groupId->toInt(),
                'module_id' => $module->id()->toInt(),
            ]);

            foreach ($rooms as $room) {
                $this->synchronizeRoomMembersForRoom($room);
            }
        }
    }

    public function synchronizeRoomMembersForRoom(Moodle\Domain\Room $room): void
    {
        $groupId = $room->groupId();

        if (!$groupId instanceof Moodle\Domain\GroupId) {
            $groupId = Moodle\Domain\GroupId::fromInt(0);
        } // Moodle wants zero instead of null

        $module = $this->moduleRepository->findOneBy([
            'id' => $room->moduleId()->toInt(),
        ]);

        if (!$module instanceof Moodle\Domain\Module) {
            throw new \RuntimeException(\sprintf(
                'Module with id "%d" was not found.',
                $room->moduleId()->toInt(),
            ));
        }

        $users = $this->userRepository->findAllUsersEnrolledInCourseAndGroup(
            $module->courseId(),
            $groupId,
        );

        $matrixUserIdsOfUsersInTheRoom = $this->api->listUsers($room->matrixRoomId());

        $matrixUserIdOfBot = $this->api->whoAmI();

        /** @var array<int, \mod_matrix\Matrix\Domain\UserId> $matrixUserIdsOfUsersAllowedInTheRoom */
        $matrixUserIdsOfUsersAllowedInTheRoom = [
            $this->api->whoAmI(),
        ];

        $powerLevels = $this->api->getState(
            $room->matrixRoomId(),
            Matrix\Domain\EventType::fromString('m.room.power_levels'),
            Matrix\Domain\StateKey::fromString(''),
        );

        $powerLevels['users'] = [
            $matrixUserIdOfBot->toString() => Matrix\Domain\PowerLevel::bot()->toInt(),
        ];

        foreach ($users as $user) {
            if (!$user->matrixUserId() instanceof Matrix\Domain\UserId) {
                continue;
            }

            if (!\in_array($user->matrixUserId(), $matrixUserIdsOfUsersInTheRoom, false)) {
                $this->api->inviteUser(
                    $user->matrixUserId(),
                    $room->matrixRoomId(),
                );
            }

            $matrixUserIdsOfUsersAllowedInTheRoom[] = $user->matrixUserId();

            $powerLevels['users'][$user->matrixUserId()->toString()] = Matrix\Domain\PowerLevel::default()->toInt();
        }

        $staff = $this->userRepository->findAllStaffInCourse($module->courseId());

        foreach ($staff as $user) {
            if (!$user->matrixUserId() instanceof Matrix\Domain\UserId) {
                continue;
            }

            if (!\in_array($user->matrixUserId(), $matrixUserIdsOfUsersInTheRoom, false)) {
                $this->api->inviteUser(
                    $user->matrixUserId(),
                    $room->matrixRoomId(),
                );
            }

            $matrixUserIdsOfUsersAllowedInTheRoom[] = $user->matrixUserId();

            $powerLevels['users'][$user->matrixUserId()->toString()] = Matrix\Domain\PowerLevel::staff()->toInt();
        }

        $this->api->setState(
            $room->matrixRoomId(),
            Matrix\Domain\EventType::fromString('m.room.power_levels'),
            Matrix\Domain\StateKey::fromString(''),
            $powerLevels,
        );

        // Kick anyone who isn't supposed to be there
        foreach ($matrixUserIdsOfUsersInTheRoom as $matrixUserId) {
            if (\in_array($matrixUserId, $matrixUserIdsOfUsersAllowedInTheRoom, false)) {
                continue;
            }

            $this->api->kickUser(
                $matrixUserId,
                $room->matrixRoomId(),
            );
        }
    }

    public function removeRoom(Moodle\Domain\Room $room): void
    {
        $matrixUserIdsOfUsersInTheRoom = $this->api->listUsers($room->matrixRoomId());

        $matrixUserIdOfBot = $this->api->whoAmI();

        foreach ($matrixUserIdsOfUsersInTheRoom as $matrixUserId) {
            if ($matrixUserId->equals($matrixUserIdOfBot)) {
                continue;
            }

            $this->api->kickUser(
                $matrixUserId,
                $room->matrixRoomId(),
            );
        }

        $this->api->kickUser(
            $matrixUserIdOfBot,
            $room->matrixRoomId(),
        );
    }
}
