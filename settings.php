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

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/mod/matrix/locallib.php');

    $settings->add(new admin_setting_heading(
        'mod_matrix/homeserver', '',
        get_string('adm_homeserver', 'matrix')
    ));
    $settings->add(new admin_setting_configtext(
        'mod_matrix/hs_url',
        get_string('adm_hsurl_name', 'matrix'),
        get_string('adm_hsurl_desc', 'matrix'),
        MATRIX_DEFAULT_SERVER_URL,
        PARAM_TEXT
    ));
    $settings->add(new admin_setting_configtext(
        'mod_matrix/access_token',
        get_string('adm_hstoken_name', 'matrix'),
        get_string('adm_hstoken_desc', 'matrix'),
        MATRIX_DEFAULT_ACCESS_TOKEN,
        PARAM_TEXT
    ));
    $settings->add(new admin_setting_configtext(
        'mod_matrix/element_url',
        get_string('adm_eleurl_name', 'matrix'),
        get_string('adm_eleurl_desc', 'matrix'),
        MATRIX_DEFAULT_ELEMENT_URL,
        PARAM_TEXT
    ));
}