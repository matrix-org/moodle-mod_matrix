<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || exit();

if ($ADMIN->fulltree) {
    require_once __DIR__ . '/locallib.php';

    $settings->add(new admin_setting_heading(
        'mod_matrix/homeserver',
        '',
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
