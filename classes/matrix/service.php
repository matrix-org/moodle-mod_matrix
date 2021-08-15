<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\matrix;

use context_course;

final class service
{
    private $api;

    private $configuration;

    public function __construct(
        api $api,
        configuration $configuration
    ) {
        $this->api = $api;
        $this->configuration = $configuration;
    }

    public function urlForRoom($roomId): string
    {
        if ('' !== trim($this->configuration->elementUrl())) {
            return $this->configuration->elementUrl() . '/#/room/' . $roomId;
        }

        return 'https://matrix.to/#/' . $roomId;
    }

    public function prepareRoomForGroup($courseId, $groupId = null): void
    {
        global $CFG, $DB;

        $course = get_course($courseId);

        $whoami = $this->api->whoami();

        $roomOptions = [
            'name' => $course->fullname,
            'topic' => $CFG->wwwroot . '/course/view.php?id=' . $courseId,
            'preset' => 'private_chat',
            'creation_content' => [
                'org.matrix.moodle.course_id' => $courseId,
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
            $group = groups_get_group($groupId);

            $existingRoomForGroup = $DB->get_record(
                'matrix_rooms',
                [
                    'course_id' => $courseId,
                    'group_id' => $group->id,
                ],
                '*',
                IGNORE_MISSING
            );

            if (!$existingRoomForGroup) {
                $roomOptions['name'] = $group->name . ': ' . $course->fullname;
                $roomOptions['creation_content']['org.matrix.moodle.group_id'] = $group->id;

                $roomId = $this->api->createRoom($roomOptions);

                $roomForGroup = new \stdClass();

                $roomForGroup->course_id = $courseId;
                $roomForGroup->group_id = $group->id;
                $roomForGroup->room_id = $roomId;
                $roomForGroup->timecreated = time();
                $roomForGroup->timemodified = 0;

                $DB->insert_record(
                    'matrix_rooms',
                    $roomForGroup
                );
            }

            $this->synchronizeRoomMembers(
                $courseId,
                $group->id
            );

            return;
        }

        $existingRoom = $DB->get_record(
            'matrix_rooms',
            [
                'course_id' => $courseId,
                'group_id' => null,
            ],
            '*',
            IGNORE_MISSING
        );

        if (!$existingRoom) {
            $roomId = $this->api->createRoom($roomOptions);

            $room = new \stdClass();

            $room->course_id = $courseId;
            $room->group_id = null;
            $room->room_id = $roomId;
            $room->timecreated = time();
            $room->timemodified = 0;

            $DB->insert_record(
                'matrix_rooms',
                $room
            );
        }

        $this->synchronizeRoomMembers($courseId);
    }

    public function resync_all($courseId = null): void
    {
        global $DB;

        $conditions = null;

        if (null !== $courseId) {
            $conditions = [
                'course_id' => $courseId,
            ];
        }

        $rooms = $DB->get_records(
            'matrix_rooms',
            $conditions
        );

        foreach ($rooms as $room) {
            $this->synchronizeRoomMembers(
                $room->course_id,
                $room->group_id
            );
        }
    }

    public function synchronizeRoomMembers($courseId, $groupId = null): void
    {
        global $DB;

        if (0 == $groupId) {
            $groupId = null;
        } // we treat zero as null, but Moodle doesn't

        $room = $DB->get_record(
            'matrix_rooms',
            [
                'course_id' => $courseId,
                'group_id' => $groupId,
            ],
            '*',
            IGNORE_MISSING
        );

        if (!$room) {
            return; // nothing to do
        }

        if (null == $groupId) {
            $groupId = 0;
        } // Moodle wants zero instead of null

        $context = context_course::instance($courseId);

        $users = get_enrolled_users(
            $context,
            'mod/matrix:view',
            $groupId
        ); // assoc of uid => user

        if (!$users) {
            $users = [];
        } // use an empty array

        $allowedUserIds = [
            $this->api->whoami(),
        ];

        $joinedUserIds = $this->api->getEffectiveJoins($room->room_id);

        foreach ($users as $user) {
            profile_load_custom_fields($user);

            $profile = $user->profile;

            if (!$profile) {
                continue;
            }

            $matrixUserId = $profile['matrix_user_id'];

            if (!$matrixUserId) {
                continue;
            }

            $allowedUserIds[] = $matrixUserId;

            if (in_array($matrixUserId, $joinedUserIds)) {
                continue;
            }

            $this->api->inviteUser(
                $matrixUserId,
                $room->room_id
            );
        }

        // Get all the staff users
        $staff = get_users_by_capability(
            $context,
            'mod/matrix:staff'
        );

        $powerLevels = $this->api->getState(
            $room->room_id,
            'm.room.power_levels',
            ''
        );

        $powerLevels['users'] = [
            $this->api->whoami() => 100,
        ];

        foreach ($staff as $user) {
            profile_load_custom_fields($user);

            $profile = $user->profile;

            if (!$profile) {
                continue;
            }

            $matrixUserId = $profile['matrix_user_id'];

            if (!$matrixUserId) {
                continue;
            }

            $allowedUserIds[] = $matrixUserId;

            if (!in_array($matrixUserId, $joinedUserIds)) {
                $this->api->inviteUser(
                    $matrixUserId,
                    $room->room_id
                );
            }

            $powerLevels['users'][$matrixUserId] = 99;
        }

        $this->api->setState(
            $room->room_id,
            'm.room.power_levels',
            '',
            $powerLevels
        );

        // Kick anyone who isn't supposed to be there
        foreach ($joinedUserIds as $matrixUserId) {
            if (in_array($matrixUserId, $allowedUserIds)) {
                continue;
            }

            $this->api->kickUser(
                $matrixUserId,
                $room->room_id
            );
        }
    }
}