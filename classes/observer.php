<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_matrix;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot . '/mod/matrix/locallib.php');
require($CFG->dirroot.'/mod/matrix/vendor/autoload.php');
require_once($CFG->dirroot.'/mod/matrix/classes/bot_client.php');

class observer {
    public static function observe_group_member_change($event) {
        global $DB;

        $instances = $DB->get_records('matrix', array('course' => $event->courseid));
        if (!$instances || sizeof($instances) <= 0) return; // no instance means no room

        matrix_sync_room_members($event->courseid, $event->objectid);
    }

    public static function observe_group_created(\core\event\group_created $event) {
        global $DB;

        $instances = $DB->get_records('matrix', array('course' => $event->courseid));
        if (!$instances || sizeof($instances) <= 0) return; // no instance means no room

        matrix_prepare_group_room($event->courseid, $event->objectid);
    }

    public static function observe_role_change($event) {
        matrix_resync_all(null); // ALL the rooms
    }

    public static function observe_enrolment_change($event) {
        matrix_resync_all($event->courseid);
    }
}