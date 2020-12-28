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

defined('MOODLE_INTERNAL') || die();

function xmldb_matrix_upgrade($oldversion=0) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2020110901) {
        $table = new xmldb_table('matrix');

        $field = new xmldb_field('name');
        $field->set_attributes(XMLDB_TYPE_CHAR, 255, false, true, false, 'Matrix Chat');
        $dbman->add_field($table, $field);

        $field = new xmldb_field('type');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 2, false, true, false, 0);
        $dbman->add_field($table, $field);

        upgrade_mod_savepoint(true, 2020110901, 'matrix');
    }

    if ($oldversion < 2020110948) {
        $table = new xmldb_table('matrix_rooms');

        $field = new xmldb_field('course');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 10, true, false, false, null);
        $dbman->rename_field($table, $field, 'course_id');

        $field = new xmldb_field('group');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 10, true, false, false, null);
        $dbman->rename_field($table, $field, 'group_id');

        upgrade_mod_savepoint(true, 2020110948, 'matrix');
    }

    return true;
}