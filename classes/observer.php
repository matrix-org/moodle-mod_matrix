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
    public static function onGroupMemberChange($event): void
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

        matrix\service::synchronizeRoomMembers(
            $event->courseid,
            $event->objectid
        );
    }

    public static function onGroupCreated(event\group_created $event): void
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

        matrix\service::prepareRoomForGroup(
            $event->courseid,
            $event->objectid
        );
    }

    public static function onRoleChanged(): void
    {
        matrix\service::resync_all(null); // ALL the rooms
    }

    public static function onUserEnrolmentChanged($event): void
    {
        matrix\service::resync_all($event->courseid);
    }
}
