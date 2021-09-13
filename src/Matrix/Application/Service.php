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

final class Service
{
    private $api;
    private $configuration;
    private $roomRepository;
    private $clock;

    public function __construct(
        Matrix\Application\Api $api,
        Matrix\Application\Configuration $configuration,
        Matrix\Application\RoomRepository $roomRepository,
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
        Matrix\Domain\CourseId $courseId,
        ?Matrix\Domain\GroupId $groupId = null
    ): void {
        global $CFG;

        $course = get_course($courseId->toInt());

        $whoami = $this->api->whoami();

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
                // Bot PL: 100 (exclusive rights to manage membership)
                // Staff PL: 99 (moderators)
                // Everyone else gets PL 0

                'ban' => 100,
                'invite' => 100,
                'kick' => 100,
                'events' => [
                    'm.room.name' => 100,
                    'm.room.power_levels' => 100,
                    'm.room.history_visibility' => 99,
                    'm.room.canonical_alias' => 99,
                    'm.room.avatar' => 99,
                    'm.room.tombstone' => 100,
                    'm.room.server_acl' => 100,
                    'm.room.encryption' => 100,
                    'm.room.join_rules' => 100,
                    'm.room.guest_access' => 100,
                ],
                'events_default' => 0,
                'state_default' => 99,
                'redact' => 50,
                'users' => [
                    $whoami => 100,
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

    public function synchronizeAll(?Matrix\Domain\CourseId $courseId = null): void
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
                $groupId = Matrix\Domain\GroupId::fromString($room->group_id);
            }

            $this->synchronizeRoomMembers(
                Matrix\Domain\CourseId::fromString($room->course_id),
                $groupId
            );
        }
    }

    public function synchronizeRoomMembers(
        Matrix\Domain\CourseId $courseId,
        ?Matrix\Domain\GroupId $groupId = null
    ): void {
        if (
            null !== $groupId
            && $groupId->equals(Matrix\Domain\GroupId::fromInt(0))
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
            $groupId = Matrix\Domain\GroupId::fromInt(0);
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

        $allowedUserIds = [
            $this->api->whoami(),
        ];

        $matrixRoomId = Matrix\Domain\MatrixRoomId::fromString($room->room_id);

        $joinedUserIds = $this->api->getMembersOfRoom($matrixRoomId);

        foreach ($users as $user) {
            $matrixUserId = $this->matrixUserIdOf($user);

            if (null === $matrixUserId) {
                continue;
            }

            if (!in_array($matrixUserId, $joinedUserIds)) {
                $this->api->inviteUser(
                    $matrixUserId,
                    $matrixRoomId
                );
            }

            $allowedUserIds[] = $matrixUserId;
        }

        // Get all the staff users
        $staff = get_users_by_capability(
            $context,
            'mod/matrix:staff'
        );

        $powerLevels = $this->api->getState(
            $matrixRoomId,
            'm.room.power_levels',
            ''
        );

        $powerLevels['users'] = [
            $this->api->whoami() => 100,
        ];

        foreach ($staff as $user) {
            $matrixUserId = $this->matrixUserIdOf($user);

            if (null === $matrixUserId) {
                continue;
            }

            if (!in_array($matrixUserId, $joinedUserIds)) {
                $this->api->inviteUser(
                    $matrixUserId,
                    $matrixRoomId
                );
            }

            $allowedUserIds[] = $matrixUserId;

            $powerLevels['users'][$matrixUserId] = 99;
        }

        $this->api->setState(
            $matrixRoomId,
            'm.room.power_levels',
            '',
            $powerLevels
        );

        // Kick anyone who isn't supposed to be there
        foreach ($joinedUserIds as $matrixUserId) {
            if (!in_array($matrixUserId, $allowedUserIds)) {
                $this->api->kickUser(
                    $matrixUserId,
                    $matrixRoomId
                );
            }
        }
    }

    private function matrixUserIdOf(object $user): ?string
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

        return $matrixUserId;
    }
}
