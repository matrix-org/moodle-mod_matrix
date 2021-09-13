<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

use mod_matrix\Matrix;

defined('MOODLE_INTERNAL') || exit();

require_once __DIR__ . '/vendor/autoload.php';

/** @var admin_root $ADMIN */
if ($ADMIN->fulltree) {
    /** @var admin_settingpage $settings */
    $settings->add(new admin_setting_heading(
        'mod_matrix/homeserver',
        '',
        get_string('adm_homeserver', 'matrix')
    ));

    $defaultConfiguration = Matrix\Infrastructure\Configuration::default();

    $settings->add(new admin_setting_configtext(
        'mod_matrix/hs_url',
        get_string('adm_hsurl_name', 'matrix'),
        get_string('adm_hsurl_desc', 'matrix'),
        $defaultConfiguration->hsUrl(),
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'mod_matrix/access_token',
        get_string('adm_hstoken_name', 'matrix'),
        get_string('adm_hstoken_desc', 'matrix'),
        $defaultConfiguration->accessToken(),
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'mod_matrix/element_url',
        get_string('adm_eleurl_name', 'matrix'),
        get_string('adm_eleurl_desc', 'matrix'),
        $defaultConfiguration->elementUrl(),
        PARAM_TEXT
    ));
}
