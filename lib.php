<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

use mod_matrix\bot;

defined('MOODLE_INTERNAL') || exit;

global $CFG;

function matrix_supports($feature)
{
    if (!$feature) {
        return null;
    }
    $features = [
        FEATURE_IDNUMBER => true,
        FEATURE_GROUPS => true,
        FEATURE_GROUPINGS => true,
        FEATURE_MOD_INTRO => true,
        FEATURE_BACKUP_MOODLE2 => true,
        FEATURE_COMPLETION_TRACKS_VIEWS => true,
        FEATURE_COMPLETION_HAS_RULES => true,
        FEATURE_GRADE_HAS_GRADE => false,
        FEATURE_GRADE_OUTCOMES => false,
        FEATURE_SHOW_DESCRIPTION => true,
    ];

    if (isset($features[$feature])) {
        return $features[$feature];
    }

    return null;
}

/**
 * @see https://github.com/moodle/moodle/blob/v3.9.5/course/modlib.php#L126-L131
 *
 * @param object $matrix
 */
function matrix_add_instance($matrix)
{
    global $DB;

    $matrix->timecreated = time();
    $matrix->timemodified = 0;
    $matrix->name = get_string('activity_default_name', 'matrix');
    $matrix->id = $DB->insert_record('matrix', $matrix);

    // Now try to iterate over all the courses and groups and see if any of
    // the rooms need to be created
    $groups = groups_get_all_groups($matrix->course, 0, 0, 'g.*', true);

    if (count($groups) > 0) {
        foreach ($groups as $k => $group) {
            matrix_prepare_group_room($matrix->course, $group->id);
        }
    } else {
        matrix_prepare_group_room($matrix->course, null);
    }

    return $matrix->id;
}

function matrix_update_instance($matrix)
{
    return true; // nothing to do
}

function matrix_delete_instance($matrix)
{
    global $DB;

    // TODO: Delete rooms too?

    if (!$DB->delete_records('matrix', ['id' => $matrix->id])) {
        return false;
    }

    return true;
}

function matrix_resync_all($course_id = null)
{
    global $DB;

    $conditions = null;

    if ($course_id) {
        $conditions = ['course_id' => $course_id];
    }

    $rooms = $DB->get_records('matrix_rooms', $conditions);

    foreach ($rooms as $rid => $room) {
        matrix_sync_room_members($room->course_id, $room->group_id);
    }
}

function matrix_prepare_group_room($course_id, $group_id = null)
{
    global $CFG, $DB;

    $course = get_course($course_id);

    $bot = bot::instance();

    $whoami = $bot->whoami();

    $room_opts = [
        'name' => $course->fullname,
        'topic' => $CFG->wwwroot . '/course/view.php?id=' . $course_id,
        'preset' => 'private_chat',
        'creation_content' => [
            'org.matrix.moodle.course_id' => $course_id,
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

    if ($group_id) {
        $group = groups_get_group($group_id);
        $existing_mapping = $DB->get_record('matrix_rooms', ['course_id' => $course_id, 'group_id' => $group->id], '*', IGNORE_MISSING);

        if (!$existing_mapping) {
            $room_opts['name'] = $group->name . ': ' . $course->fullname;
            $room_opts['creation_content']['org.matrix.moodle.group_id'] = $group->id;
            $room_id = $bot->create_room($room_opts);

            $room_mapping = new stdClass();
            $room_mapping->course_id = $course_id;
            $room_mapping->group_id = $group->id;
            $room_mapping->room_id = $room_id;
            $room_mapping->timecreated = time();
            $room_mapping->timemodified = 0;
            $DB->insert_record('matrix_rooms', $room_mapping);
        }
        matrix_sync_room_members($course_id, $group->id);
    } else {
        $existing_mapping = $DB->get_record('matrix_rooms', ['course_id' => $course_id, 'group_id' => null], '*', IGNORE_MISSING);

        if (!$existing_mapping) {
            $room_id = $bot->create_room($room_opts);

            $room_mapping = new stdClass();
            $room_mapping->course_id = $course_id;
            $room_mapping->group_id = null;
            $room_mapping->room_id = $room_id;
            $room_mapping->timecreated = time();
            $room_mapping->timemodified = 0;
            $DB->insert_record('matrix_rooms', $room_mapping);
        }
        matrix_sync_room_members($course_id, null);
    }
}

function matrix_sync_room_members($course_id, $group_id = null)
{
    global $DB;
    $bot = bot::instance();

    if (0 == $group_id) {
        $group_id = null;
    } // we treat zero as null, but Moodle doesn't

    $mapping = $DB->get_record('matrix_rooms', ['course_id' => $course_id, 'group_id' => $group_id], '*', IGNORE_MISSING);

    if (!$mapping) {
        return; // nothing to do
    }

    if (null == $group_id) {
        $group_id = 0;
    } // Moodle wants zero instead of null

    $cc = context_course::instance($course_id);
    $users = get_enrolled_users($cc, 'mod/matrix:view', $group_id); // assoc of uid => user

    if (!$users) {
        $users = [];
    } // use an empty array

    $allowed_user_ids = [$bot->whoami()];
    $joined_user_ids = $bot->get_effective_joins($mapping->room_id);

    foreach ($users as $uid => $user) {
        profile_load_custom_fields($user);
        $profile = $user->profile;

        if (!$profile) {
            continue;
        }

        $mxid = $profile['matrix_user_id'];

        if (!$mxid) {
            continue;
        }

        $allowed_user_ids[] = $mxid;

        if (!in_array($mxid, $joined_user_ids)) {
            $bot->invite_user($mxid, $mapping->room_id);
        }
    }

    // Get all the staff users
    $staff = get_users_by_capability($cc, 'mod/matrix:staff');
    $pls = $bot->get_state($mapping->room_id, 'm.room.power_levels', '');
    $pls['users'] = [
        $bot->whoami() => 100,
    ];

    foreach ($staff as $uid => $user) {
        profile_load_custom_fields($user);
        $profile = $user->profile;

        if (!$profile) {
            continue;
        }

        $mxid = $profile['matrix_user_id'];

        if (!$mxid) {
            continue;
        }

        $allowed_user_ids[] = $mxid;

        if (!in_array($mxid, $joined_user_ids)) {
            $bot->invite_user($mxid, $mapping->room_id);
        }

        $pls['users'][$mxid] = 99;
    }
    $bot->set_state($mapping->room_id, 'm.room.power_levels', '', $pls);

    // Kick anyone who isn't supposed to be there
    foreach ($joined_user_ids as $mxid) {
        if (!in_array($mxid, $allowed_user_ids)) {
            $bot->kick_user($mxid, $mapping->room_id);
        }
    }
}

function matrix_make_room_url($room_id)
{
    $conf = get_config('mod_matrix');

    if ($conf->element_url) {
        return $conf->element_url . '/#/room/' . $room_id;
    }

    return 'https://matrix.to/#/' . $room_id;
}

// TODO: Events API
// - Group edits
// - Course enrollment edits
// - Custom field (profile) updates
// - Role changes
