<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

use mod_matrix\matrix;

defined('MOODLE_INTERNAL') || exit;

global $CFG;

/**
 * @see https://github.com/moodle/moodle/blob/v3.9.5/lib/moodlelib.php#L8139-L8175
 *
 * @param string $feature
 *
 * @return null|bool
 */
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
 * @see https://docs.moodle.org/dev/Activity_modules#lib.php
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
    $groups = groups_get_all_groups(
        $matrix->course,
        0,
        0,
        'g.*',
        true
    );

    if (count($groups) > 0) {
        foreach ($groups as $group) {
            matrix::prepareRoomForGroup(
                $matrix->course,
                $group->id
            );
        }
    } else {
        matrix::prepareRoomForGroup($matrix->course);
    }

    return $matrix->id;
}

/**
 * @see https://docs.moodle.org/dev/Activity_modules#lib.php
 * @see https://github.com/moodle/moodle/blob/v3.9.5/course/modlib.php#L611-L614
 *
 * @param object $matrix
 */
function matrix_update_instance($matrix)
{
    return true; // nothing to do
}

/**
 * @see https://docs.moodle.org/dev/Activity_modules#lib.php
 * @see https://github.com/moodle/moodle/blob/v3.9.5/course/lib.php#L1034-L1040
 * @see https://github.com/moodle/moodle/blob/v3.9.5/course/lib.php#L1054-L1057
 *
 * @param object $matrix
 */
function matrix_delete_instance($matrix): bool
{
    global $DB;

    // TODO: Delete rooms too?

    $hasDeletedInstance = $DB->delete_records('matrix', [
        'id' => $matrix->id,
    ]);

    if (!$hasDeletedInstance) {
        return false;
    }

    return true;
}

// TODO: Events API
// - Group edits
// - Course enrollment edits
// - Custom field (profile) updates
// - Role changes
