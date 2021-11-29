<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

\defined('MOODLE_INTERNAL') || exit();

if (!isset($string)) {
    throw new \RuntimeException('Expected variable $string to be set at this point.');
}

if (!\is_array($string)) {
    throw new \RuntimeException(\sprintf(
        'Expected variable $string to be an array, got %s instead.',
        \is_object($string) ? \get_class($string) : \gettype($string),
    ));
}

/** @var array<string, string> $string */
$string = \array_merge($string, [
    // ?
    'modulename' => 'Matrix',
    'modulenameplural' => 'Matrix',
    'pluginadministration' => 'Matrix administration',
    'pluginname' => 'Matrix',
    // lib.php
    'activity_default_name' => 'Matrix Chat',
    // settings.php
    'settings_access_token_description' => 'The access token the Matrix bot should use to authenticate with your Homeserver',
    'settings_access_token_name' => 'Access Token',
    'settings_element_url_description' => 'The URL to your Element Web instance. If not supplied/empty, matrix.to URLs will be generated instead',
    'settings_element_url_name' => 'Element Web URL',
    'settings_homeserver_heading' => 'Homeserver Settings',
    'settings_homeserver_url_description' => 'The URL where the Matrix bot should connect to your Homeserver',
    'settings_homeserver_url_name' => 'Homeserver URL',
    // view.php
    'view_alert_many_rooms' => 'You can see multiple rooms for this course - please pick the one you would like to visit',
    'view_button_join_room' => 'Join room',
    'view_error_no_groups' => 'There are no groups.',
    'view_error_no_room_in_group' => 'There is no room in this group.',
    'view_error_no_rooms' => 'There are no rooms to show.',
    'view_error_no_visible_groups' => 'There are no visible groups.',
    // ?
    'matrix:addinstance' => 'Add/edit Matrix room links',
    'matrix:staff' => 'Treat the user as a staff user in Matrix rooms',
    'matrix:view' => 'View Matrix room links',
]);
