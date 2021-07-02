<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

use mod_matrix\matrix;

defined('MOODLE_INTERNAL') || exit();

/** @var admin_root $ADMIN */
if ($ADMIN->fulltree) {
    /** @var admin_settingpage $settings */
    $settings->add(new admin_setting_heading(
        'mod_matrix/homeserver',
        '',
        get_string('adm_homeserver', 'matrix')
    ));

    $settings->add(new admin_setting_configtext(
        'mod_matrix/hs_url',
        get_string('adm_hsurl_name', 'matrix'),
        get_string('adm_hsurl_desc', 'matrix'),
        matrix::DEFAULT_HS_URL,
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'mod_matrix/access_token',
        get_string('adm_hstoken_name', 'matrix'),
        get_string('adm_hstoken_desc', 'matrix'),
        matrix::DEFAULT_ACCESS_TOKEN,
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'mod_matrix/element_url',
        get_string('adm_eleurl_name', 'matrix'),
        get_string('adm_eleurl_desc', 'matrix'),
        matrix::DEFAULT_ELEMENT_URL,
        PARAM_TEXT
    ));
}
