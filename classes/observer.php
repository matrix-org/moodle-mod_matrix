<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_matrix;

defined('MOODLE_INTERNAL') || exit;

global $CFG;

require_once $CFG->dirroot . '/mod/matrix/locallib.php';

require $CFG->dirroot . '/mod/matrix/vendor/autoload.php';

require_once $CFG->dirroot . '/mod/matrix/classes/bot_client.php';

class observer
{
    public static function observe_group_member_change($event)
    {
        global $DB;

        $instances = $DB->get_records('matrix', ['course' => $event->courseid]);

        if (!$instances || count($instances) <= 0) {
            return;
        } // no instance means no room

        matrix_sync_room_members($event->courseid, $event->objectid);
    }

    public static function observe_group_created(\core\event\group_created $event)
    {
        global $DB;

        $instances = $DB->get_records('matrix', ['course' => $event->courseid]);

        if (!$instances || count($instances) <= 0) {
            return;
        } // no instance means no room

        matrix_prepare_group_room($event->courseid, $event->objectid);
    }

    public static function observe_role_change($event)
    {
        matrix_resync_all(null); // ALL the rooms
    }

    public static function observe_enrolment_change($event)
    {
        matrix_resync_all($event->courseid);
    }
}
