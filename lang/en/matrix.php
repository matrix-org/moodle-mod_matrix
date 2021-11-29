<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

use mod_matrix\Moodle;

\defined('MOODLE_INTERNAL') || exit();

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * @see https://github.com/moodle/moodle/blob/v3.9.5/lib/classes/string_manager_standard.php#L171-L177
 */

/** @var array<string, string> $string */
$string = \array_merge($string, [
    // ?
    'modulename' => 'Matrix',
    'modulenameplural' => 'Matrix',
    'pluginadministration' => 'Matrix administration',
    'pluginname' => 'Matrix',
    // mod_form.php
    Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_HEADER => 'Basic module settings',
    Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_DEFAULT => 'Matrix Chat',
    Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_ERROR_MAXLENGTH => \sprintf(
        'A name can not be longer than %d characters. Fewer characters than that will probably be better.',
        Moodle\Domain\ModuleName::LENGTH_MAX,
    ),
    Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_ERROR_REQUIRED => 'A name is required.',
    Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_HELP => 'A good name will make it easier for users to tell Matrix rooms apart.',
    Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_NAME => 'Name',
    // settings.php
    Moodle\Infrastructure\Internationalization::SETTINGS_ACCESS_TOKEN_DESCRIPTION => 'The access token the Matrix bot should use to authenticate with your Homeserver',
    Moodle\Infrastructure\Internationalization::SETTINGS_ACCESS_TOKEN_NAME => 'Access Token',
    Moodle\Infrastructure\Internationalization::SETTINGS_ELEMENT_URL_DESCRIPTION => 'The URL to your Element Web instance. If provided, the will open in the browser. If left empty, users will have a chance to select which application to use to open the chats via https://matrix.to.',
    Moodle\Infrastructure\Internationalization::SETTINGS_ELEMENT_URL_NAME => 'Element Web URL',
    Moodle\Infrastructure\Internationalization::SETTINGS_HOMESERVER_HEADING => 'Homeserver Settings',
    Moodle\Infrastructure\Internationalization::SETTINGS_HOMESERVER_URL_DESCRIPTION => 'The URL where the Matrix bot should connect to your Homeserver',
    Moodle\Infrastructure\Internationalization::SETTINGS_HOMESERVER_URL_NAME => 'Homeserver URL',
    // view.php
    Moodle\Infrastructure\Internationalization::VIEW_ALERT_MANY_ROOMS => 'You can see multiple rooms for this course - please pick the one you would like to visit',
    Moodle\Infrastructure\Internationalization::VIEW_BUTTON_JOIN_ROOM => 'Join room',
    Moodle\Infrastructure\Internationalization::VIEW_ERROR_NO_GROUPS => 'There are no groups.',
    Moodle\Infrastructure\Internationalization::VIEW_ERROR_NO_ROOM_IN_GROUP => 'There is no room in this group.',
    Moodle\Infrastructure\Internationalization::VIEW_ERROR_NO_ROOMS => 'There are no rooms to show.',
    Moodle\Infrastructure\Internationalization::VIEW_ERROR_NO_VISIBLE_GROUPS => 'There are no visible groups.',
    // ?
    'matrix:addinstance' => 'Add/edit Matrix room links',
    'matrix:staff' => 'Treat the user as a staff user in Matrix rooms',
    'matrix:view' => 'View Matrix room links',
]);
