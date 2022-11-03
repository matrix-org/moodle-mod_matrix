<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

use mod_matrix\Matrix;
use mod_matrix\Moodle;
use mod_matrix\Plugin;

\defined('MOODLE_INTERNAL') || exit();

require_once __DIR__ . '/vendor/autoload.php';

/**
 * @see https://docs.moodle.org/dev/Plugin_files#settings.php
 * @see https://docs.moodle.org/dev/Admin_settings
 */

/** @var admin_root $ADMIN */
if ($ADMIN->fulltree) {
    /** @var admin_settingpage $settings */
    $settings->add(new admin_setting_heading(
        'mod_matrix/homeserver',
        '',
        get_string(
            Plugin\Infrastructure\Internationalization::SETTINGS_HOMESERVER_HEADING,
            Plugin\Application\Plugin::NAME,
        ),
    ));

    $defaultConfiguration = Plugin\Application\Configuration::default();

    $settings->add(new admin_setting_configtext(
        'mod_matrix/homeserver_url',
        get_string(
            Plugin\Infrastructure\Internationalization::SETTINGS_HOMESERVER_URL_NAME,
            Plugin\Application\Plugin::NAME,
        ),
        get_string(
            Plugin\Infrastructure\Internationalization::SETTINGS_HOMESERVER_URL_DESCRIPTION,
            Plugin\Application\Plugin::NAME,
        ),
        $defaultConfiguration->homeserverUrl()->toString(),
        PARAM_TEXT,
    ));

    $settings->add(new admin_setting_configtext(
        'mod_matrix/access_token',
        get_string(
            Plugin\Infrastructure\Internationalization::SETTINGS_ACCESS_TOKEN_NAME,
            Plugin\Application\Plugin::NAME,
        ),
        get_string(
            Plugin\Infrastructure\Internationalization::SETTINGS_ACCESS_TOKEN_DESCRIPTION,
            Plugin\Application\Plugin::NAME,
        ),
        $defaultConfiguration->accessToken()->toString(),
        PARAM_TEXT,
    ));

    $settings->add(new admin_setting_configtext(
        'mod_matrix/element_url',
        get_string(
            Plugin\Infrastructure\Internationalization::SETTINGS_ELEMENT_URL_NAME,
            Plugin\Application\Plugin::NAME,
        ),
        get_string(
            Plugin\Infrastructure\Internationalization::SETTINGS_ELEMENT_URL_DESCRIPTION,
            Plugin\Application\Plugin::NAME,
        ),
        $defaultConfiguration->elementUrl()->toString(),
        PARAM_TEXT,
    ));
}
