<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

use mod_matrix\Matrix;
use mod_matrix\Moodle;

\defined('MOODLE_INTERNAL') || exit();

require_once __DIR__ . '/vendor/autoload.php';

/** @var admin_root $ADMIN */
if ($ADMIN->fulltree) {
    /** @var admin_settingpage $settings */
    $settings->add(new admin_setting_heading(
        'mod_matrix/homeserver',
        '',
        get_string(
            Moodle\Infrastructure\Internationalization::SETTINGS_HOMESERVER_HEADING,
            Moodle\Application\Plugin::NAME,
        ),
    ));

    $defaultConfiguration = Matrix\Application\Configuration::default();

    $settings->add(new admin_setting_configtext(
        'mod_matrix/homeserver_url',
        get_string(
            Moodle\Infrastructure\Internationalization::SETTINGS_HOMESERVER_URL_NAME,
            Moodle\Application\Plugin::NAME,
        ),
        get_string(
            Moodle\Infrastructure\Internationalization::SETTINGS_HOMESERVER_URL_DESCRIPTION,
            Moodle\Application\Plugin::NAME,
        ),
        $defaultConfiguration->homeserverUrl(),
        PARAM_TEXT,
    ));

    $settings->add(new admin_setting_configtext(
        'mod_matrix/access_token',
        get_string(
            Moodle\Infrastructure\Internationalization::SETTINGS_ACCESS_TOKEN_NAME,
            Moodle\Application\Plugin::NAME,
        ),
        get_string(
            Moodle\Infrastructure\Internationalization::SETTINGS_ACCESS_TOKEN_DESCRIPTION,
            Moodle\Application\Plugin::NAME,
        ),
        $defaultConfiguration->accessToken(),
        PARAM_TEXT,
    ));

    $settings->add(new admin_setting_configtext(
        'mod_matrix/element_url',
        get_string(
            Moodle\Infrastructure\Internationalization::SETTINGS_ELEMENT_URL_NAME,
            Moodle\Application\Plugin::NAME,
        ),
        get_string(
            Moodle\Infrastructure\Internationalization::SETTINGS_ELEMENT_URL_DESCRIPTION,
            Moodle\Application\Plugin::NAME,
        ),
        $defaultConfiguration->elementUrl(),
        PARAM_TEXT,
    ));
}
