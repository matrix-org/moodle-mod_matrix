<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Matrix\Application;

use context_course;
use Ergebnis\Clock;
use mod_matrix\Matrix;
use mod_matrix\Moodle;

final class Service
{
    private $api;
    private $configuration;
    private $roomRepository;
    private $clock;

    public function __construct(
        Matrix\Application\Api $api,
        Matrix\Application\Configuration $configuration,
        Moodle\Application\RoomRepository $roomRepository,
        Clock\Clock $clock
    ) {
        $this->api = $api;
        $this->configuration = $configuration;
        $this->roomRepository = $roomRepository;
        $this->clock = $clock;
    }

    public function urlForRoom($roomId): string
    {
        if ('' !== trim($this->configuration->elementUrl())) {
            return $this->configuration->elementUrl() . '/#/room/' . $roomId;
        }

        return 'https://matrix.to/#/' . $roomId;
    }

    public function prepareRoomForGroup(
        Moodle\Domain\CourseId $courseId,
        ?Moodle\Domain\GroupId $groupId = null
    ): void {
        global $CFG;

        $course = get_course($courseId->toInt());

        $whoami = $this->api->whoami();

        $botPowerLevel = Matrix\Domain\PowerLevel::bot();
        $staffPowerLevel = Matrix\Domain\PowerLevel::staff();
        $redactorPowerLevel = Matrix\Domain\PowerLevel::redactor();

        $roomOptions = [
            'name' => $course->fullname,
            'topic' => sprintf(
                '%s/course/view.php?id=%d',
                $CFG->wwwroot,
                $courseId->toInt()
            ),
            'preset' => 'private_chat',
            'creation_content' => [
                'org.matrix.moodle.course_id' => $courseId->toInt(),
                //'org.matrix.moodle.group_id' => 'undefined'
            ],
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
            'initial_state' => [
                [
                    'type' => 'm.room.guest_access',
                    'state_key' => '',
                    'content' => [
                        'guest_access' => 'forbidden',
                    ],
                ],
            ],
        ];

        if (null !== $groupId) {
            $group = groups_get_group($groupId->toInt());

            $existingRoomForGroup = $this->roomRepository->findOneBy([
                'course_id' => $courseId->toInt(),
                'group_id' => $groupId->toInt(),
            ]);

            if (!$existingRoomForGroup) {
                $roomOptions['name'] = $group->name . ': ' . $course->fullname;
                $roomOptions['creation_content']['org.matrix.moodle.group_id'] = $groupId->toInt();

                $roomId = $this->api->createRoom($roomOptions);

                $roomForGroup = new \stdClass();

                $roomForGroup->course_id = $courseId->toInt();
                $roomForGroup->group_id = $groupId->toInt();
                $roomForGroup->room_id = $roomId->toString();
                $roomForGroup->timecreated = $this->clock->now()->getTimestamp();
                $roomForGroup->timemodified = 0;

                $this->roomRepository->save($roomForGroup);
            }

            $this->synchronizeRoomMembers(
                $courseId,
                $groupId
            );

            return;
        }

        $existingRoom = $this->roomRepository->findOneBy([
            'course_id' => $courseId->toInt(),
            'group_id' => null,
        ]);

        if (!$existingRoom) {
            $roomId = $this->api->createRoom($roomOptions);

            $room = new \stdClass();

            $room->course_id = $courseId->toInt();
            $room->group_id = null;
            $room->room_id = $roomId->toString();
            $room->timecreated = $this->clock->now()->getTimestamp();
            $room->timemodified = 0;

            $this->roomRepository->save($room);
        }

        $this->synchronizeRoomMembers($courseId);
    }

    public function synchronizeAll(?Moodle\Domain\CourseId $courseId = null): void
    {
        $conditions = null;

        if (null !== $courseId) {
            $conditions = [
                'course_id' => $courseId->toInt(),
            ];
        }

        $rooms = $this->roomRepository->findAllBy($conditions);

        foreach ($rooms as $room) {
            $groupId = null;

            if (null !== $room->group_id) {
                $groupId = Moodle\Domain\GroupId::fromString($room->group_id);
            }

            $this->synchronizeRoomMembers(
                Moodle\Domain\CourseId::fromString($room->course_id),
                $groupId
            );
        }
    }

    public function synchronizeRoomMembers(
        Moodle\Domain\CourseId $courseId,
        ?Moodle\Domain\GroupId $groupId = null
    ): void {
        if (
            null !== $groupId
            && $groupId->equals(Moodle\Domain\GroupId::fromInt(0))
        ) {
            $groupId = null;
        } // we treat zero as null, but Moodle doesn't

        if (null !== $groupId) {
            $room = $this->roomRepository->findOneBy([
                'course_id' => $courseId->toInt(),
                'group_id' => $groupId->toInt(),
            ]);
        } else {
            $room = $this->roomRepository->findOneBy([
                'course_id' => $courseId->toInt(),
                'group_id' => null,
            ]);
        }

        if (!$room) {
            return; // nothing to do
        }

        if (null === $groupId) {
            $groupId = Moodle\Domain\GroupId::fromInt(0);
        } // Moodle wants zero instead of null

        $context = context_course::instance($courseId->toInt());

        $users = get_enrolled_users(
            $context,
            'mod/matrix:view',
            $groupId->toInt()
        ); // assoc of uid => user

        if (!$users) {
            $users = [];
        } // use an empty array

        $roomId = Matrix\Domain\RoomId::fromString($room->room_id);

        $matrixUserIdsOfUsersInTheRoom = $this->api->getMembersOfRoom($roomId);

        $matrixUserIdOfBot = $this->api->whoami();

        /** @var array<int, \mod_matrix\Matrix\Domain\UserId> $matrixUserIdsOfUsersAllowedInTheRoom */
        $matrixUserIdsOfUsersAllowedInTheRoom = [
            $this->api->whoami(),
        ];

        $powerLevels = $this->api->getState(
            $roomId,
            'm.room.power_levels',
            ''
        );

        $powerLevels['users'] = [
            $matrixUserIdOfBot->toString() => Matrix\Domain\PowerLevel::bot()->toInt(),
        ];

        foreach ($users as $user) {
            $matrixUserId = $this->matrixUserIdOf($user);

            if (null === $matrixUserId) {
                continue;
            }

            if (!in_array($matrixUserId, $matrixUserIdsOfUsersInTheRoom, false)) {
                $this->api->inviteUser(
                    $matrixUserId,
                    $roomId
                );
            }

            $matrixUserIdsOfUsersAllowedInTheRoom[] = $matrixUserId;

            $powerLevels['users'][$matrixUserId->toString()] = Matrix\Domain\PowerLevel::default()->toInt();
        }

        // Get all the staff users
        $staff = get_users_by_capability(
            $context,
            'mod/matrix:staff'
        );

        foreach ($staff as $user) {
            $matrixUserId = $this->matrixUserIdOf($user);

            if (null === $matrixUserId) {
                continue;
            }

            if (!in_array($matrixUserId, $matrixUserIdsOfUsersInTheRoom, false)) {
                $this->api->inviteUser(
                    $matrixUserId,
                    $roomId
                );
            }

            $matrixUserIdsOfUsersAllowedInTheRoom[] = $matrixUserId;

            $powerLevels['users'][$matrixUserId->toString()] = Matrix\Domain\PowerLevel::staff()->toInt();
        }

        $this->api->setState(
            $roomId,
            'm.room.power_levels',
            '',
            $powerLevels
        );

        // Kick anyone who isn't supposed to be there
        foreach ($matrixUserIdsOfUsersInTheRoom as $matrixUserId) {
            if (!in_array($matrixUserId, $matrixUserIdsOfUsersAllowedInTheRoom, false)) {
                $this->api->kickUser(
                    $matrixUserId,
                    $roomId
                );
            }
        }
    }

    private function matrixUserIdOf(object $user): ?Matrix\Domain\UserId
    {
        profile_load_custom_fields($user);

        if (!property_exists($user, 'profile')) {
            return null;
        }

        if (!is_array($user->profile)) {
            return null;
        }

        if (!array_key_exists('matrix_user_id', $user->profile)) {
            return null;
        }

        $matrixUserId = $user->profile['matrix_user_id'];

        if (!is_string($matrixUserId)) {
            return null;
        }

        if ('' === trim($matrixUserId)) {
            return null;
        }

        return Matrix\Domain\UserId::fromString($matrixUserId);
    }
}
