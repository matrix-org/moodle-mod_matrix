<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

use mod_matrix\Matrix;

\defined('MOODLE_INTERNAL') || exit();

require_once __DIR__ . '/vendor/autoload.php';

/** @var admin_root $ADMIN */
if ($ADMIN->fulltree) {
    /** @var admin_settingpage $settings */
    $settings->add(new admin_setting_heading(
        'mod_matrix/homeserver',
        '',
        get_string('settings_homeserver_heading', 'matrix'),
    ));

    $defaultConfiguration = Matrix\Application\Configuration::default();

    $settings->add(new admin_setting_configtext(
        'mod_matrix/hs_url',
        get_string('settings_homeserver_url_name', 'matrix'),
        get_string('settings_homeserver_url_description', 'matrix'),
        $defaultConfiguration->hsUrl(),
        PARAM_TEXT,
    ));

    $settings->add(new admin_setting_configtext(
        'mod_matrix/access_token',
        get_string('settings_access_token_name', 'matrix'),
        get_string('settings_access_token_description', 'matrix'),
        $defaultConfiguration->accessToken(),
        PARAM_TEXT,
    ));

    $settings->add(new admin_setting_configtext(
        'mod_matrix/element_url',
        get_string('settings_element_url_name', 'matrix'),
        get_string('settings_element_url_description', 'matrix'),
        $defaultConfiguration->elementUrl(),
        PARAM_TEXT,
    ));
}
