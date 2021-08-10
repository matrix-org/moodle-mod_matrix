<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix;

use core\event;

defined('MOODLE_INTERNAL') || exit;

class observer
{
    public static function observe_group_member_change($event)
    {
        global $DB;

        $instances = $DB->get_records(
            'matrix',
            [
                'course' => $event->courseid,
            ]
        );

        if ([] === $instances) {
            return;
        }

        matrix::sync_room_members(
            $event->courseid,
            $event->objectid
        );
    }

    public static function observe_group_created(event\group_created $event)
    {
        global $DB;

        $instances = $DB->get_records(
            'matrix',
            [
                'course' => $event->courseid,
            ]
        );

        if ([] === $instances) {
            return;
        }

        matrix::prepare_group_room(
            $event->courseid,
            $event->objectid
        );
    }

    public static function observe_role_change()
    {
        matrix::resync_all(null); // ALL the rooms
    }

    public static function observe_enrolment_change($event)
    {
        matrix::resync_all($event->courseid);
    }
}
