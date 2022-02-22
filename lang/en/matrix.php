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
    Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME => 'Name',
    Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_DEFAULT => 'Matrix Chat',
    Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_ERROR_MAXLENGTH => \sprintf(
        'A name can not be longer than %d characters. Fewer characters than that will probably be better.',
        Moodle\Domain\ModuleName::LENGTH_MAX,
    ),
    Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_ERROR_REQUIRED => 'A name is required.',
    Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_HELP => 'A good name will make it easier for users to tell Matrix rooms apart.',
    Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_NAME => 'Name',
    Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TARGET_ERROR_REQUIRED => 'A target is required. Where should the chat be opened?',
    Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TARGET_LABEL_ELEMENT_URL => 'via configured Element URL',
    Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TARGET_LABEL_MATRIX_TO => 'via https://matrix.to',
    Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TARGET_NAME => 'Open chat in browser',
    Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TOPIC_HELP => 'A topic will be displayed in the Matrix room, and could remind members of its purpose.',
    Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TOPIC_NAME => 'Topic',
    // settings.php
    Moodle\Infrastructure\Internationalization::SETTINGS_ACCESS_TOKEN_DESCRIPTION => 'The access token the Matrix bot should use to authenticate with your Homeserver',
    Moodle\Infrastructure\Internationalization::SETTINGS_ACCESS_TOKEN_NAME => 'Access Token',
    Moodle\Infrastructure\Internationalization::SETTINGS_ELEMENT_URL_DESCRIPTION => 'The URL to your Element Web instance. If left empty, the Matrix chat will open via https://matrix.to. If provided, teachers can choose per module whether the Matrix chat will open via configured Element Web instance or https://matrix.to.',
    Moodle\Infrastructure\Internationalization::SETTINGS_ELEMENT_URL_NAME => 'Element Web URL',
    Moodle\Infrastructure\Internationalization::SETTINGS_HOMESERVER_HEADING => 'Homeserver Settings',
    Moodle\Infrastructure\Internationalization::SETTINGS_HOMESERVER_URL_DESCRIPTION => 'The URL where the Matrix bot should connect to your Homeserver',
    Moodle\Infrastructure\Internationalization::SETTINGS_HOMESERVER_URL_NAME => 'Homeserver URL',
    // view.php
    Moodle\Infrastructure\Internationalization::ACTION_EDIT_MATRIX_USER_ID_HEADER => 'Matrix User Identifier Missing',
    Moodle\Infrastructure\Internationalization::ACTION_EDIT_MATRIX_USER_ID_INFO_SUGGESTION => 'Perhaps one of the following is your Matrix user identifier? Just guessing, though!',
    Moodle\Infrastructure\Internationalization::ACTION_EDIT_MATRIX_USER_ID_WARNING_NO_MATRIX_USER_ID => 'It appears that you have not yet provided a valid Matrix user identifier. Without it, you can not join any Matrix chat rooms. Can you provide one now?',
    Moodle\Infrastructure\Internationalization::ACTION_LIST_ROOMS_HEADER => 'Rooms',
    Moodle\Infrastructure\Internationalization::ACTION_LIST_ROOMS_WARNING_NO_ROOMS => 'There are no rooms to show.',
    Moodle\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_ERROR_MATRIX_USER_ID_INVALID => 'The Matrix user identifier you provided appears to be invalid.',
    Moodle\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_ERROR_MATRIX_USER_ID_REQUIRED => 'A Matrix user identifier is required, otherwise you can not join Matrix chat rooms.',
    Moodle\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_HEADER => 'Matrix User Identifier',
    Moodle\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_MATRIX_USER_ID_NAME => 'Matrix user identifier',
    Moodle\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_MATRIX_USER_ID_NAME_HELP => 'A Matrix user identifier looks like @localpart:domain, for example, @jane:example.org.',
    Moodle\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_MATRIX_USER_ID_NAME_SUGGESTION => 'Suggestions',
    Moodle\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_MATRIX_USER_ID_NAME_SUGGESTION_HELP => 'Too lazy to type? Perhaps one these suggestions is your Matrix user identifier?',
    // ?
    'matrix:addinstance' => 'Add/edit Matrix room links',
    'matrix:staff' => 'Treat the user as a staff user in Matrix rooms',
    'matrix:view' => 'View Matrix room links',
]);
