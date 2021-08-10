<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix;

use context_course;

final class matrix
{
    /**
     * The default client-server API URL.
     */
    public const DEFAULT_HS_URL = 'https://matrix-client.matrix.org';

    /**
     * The default access token on the given homeserver.
     */
    public const DEFAULT_ACCESS_TOKEN = '';

    /**
     * The default Element Web URL.
     */
    public const DEFAULT_ELEMENT_URL = '';

    public static function urlForRoom($roomId): string
    {
        $conf = get_config('mod_matrix');

        if ($conf->element_url) {
            return $conf->element_url . '/#/room/' . $roomId;
        }

        return 'https://matrix.to/#/' . $roomId;
    }

    public static function prepare_group_room($courseId, $groupId = null)
    {
        global $CFG, $DB;

        $course = get_course($courseId);

        $bot = bot::instance();

        $whoami = $bot->whoami();

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
                    'content' => ['guest_access' => 'forbidden'],
                ],
            ],
        ];

        if (null !== $groupId) {
            $group = groups_get_group($groupId);

            $existingMapping = $DB->get_record(
                'matrix_rooms',
                [
                    'course_id' => $courseId,
                    'group_id' => $group->id,
                ],
                '*',
                IGNORE_MISSING
            );

            if (!$existingMapping) {
                $roomOptions['name'] = $group->name . ': ' . $course->fullname;
                $roomOptions['creation_content']['org.matrix.moodle.group_id'] = $group->id;

                $roomId = $bot->createRoom($roomOptions);

                $roomMapping = new \stdClass();

                $roomMapping->course_id = $courseId;
                $roomMapping->group_id = $group->id;
                $roomMapping->room_id = $roomId;
                $roomMapping->timecreated = time();
                $roomMapping->timemodified = 0;

                $DB->insert_record('matrix_rooms', $roomMapping);
            }

            self::synchronizeRoomMembers($courseId, $group->id);

            return;
        }

        $existingMapping = $DB->get_record(
            'matrix_rooms',
            [
                'course_id' => $courseId,
                'group_id' => null,
            ],
            '*',
            IGNORE_MISSING
        );

        if (!$existingMapping) {
            $roomId = $bot->createRoom($roomOptions);

            $roomMapping = new \stdClass();

            $roomMapping->course_id = $courseId;
            $roomMapping->group_id = null;
            $roomMapping->room_id = $roomId;
            $roomMapping->timecreated = time();
            $roomMapping->timemodified = 0;

            $DB->insert_record('matrix_rooms', $roomMapping);
        }

        self::synchronizeRoomMembers($courseId, null);
    }

    public static function resync_all($courseId = null)
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
            self::synchronizeRoomMembers(
                $room->course_id,
                $room->group_id
            );
        }
    }

    public static function synchronizeRoomMembers($courseId, $groupId = null): void
    {
        global $DB;

        $bot = bot::instance();

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
            $bot->whoami(),
        ];

        $joinedUserIds = $bot->getEffectiveJoins($room->room_id);

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

            $bot->inviteUser(
                $matrixUserId,
                $room->room_id
            );
        }

        // Get all the staff users
        $staff = get_users_by_capability(
            $context,
            'mod/matrix:staff'
        );

        $powerLevels = $bot->getState(
            $room->room_id,
            'm.room.power_levels',
            ''
        );

        $powerLevels['users'] = [
            $bot->whoami() => 100,
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
                $bot->inviteUser(
                    $matrixUserId,
                    $room->room_id
                );
            }

            $powerLevels['users'][$matrixUserId] = 99;
        }

        $bot->setState(
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

            $bot->kickUser(
                $matrixUserId,
                $room->room_id
            );
        }
    }
}
