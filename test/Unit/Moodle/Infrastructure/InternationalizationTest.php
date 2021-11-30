<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Moodle\Infrastructure;

use mod_matrix\Moodle;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Moodle\Infrastructure\Internationalization
 */
final class InternationalizationTest extends Framework\TestCase
{
    public function testConstants(): void
    {
        self::assertSame('mod_form_basic_settings_header', Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_HEADER);
        self::assertSame('mod_form_basic_settings_name_default', Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_DEFAULT);
        self::assertSame('mod_form_basic_settings_name_error_maxlength', Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_ERROR_MAXLENGTH);
        self::assertSame('mod_form_basic_settings_name_error_required', Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_ERROR_REQUIRED);
        self::assertSame('mod_form_basic_settings_name_help', Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_HELP);
        self::assertSame('mod_form_basic_settings_topic_help', Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TOPIC_HELP);
        self::assertSame('mod_form_basic_settings_topic_name', Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TOPIC_NAME);
        self::assertSame('settings_access_token_description', Moodle\Infrastructure\Internationalization::SETTINGS_ACCESS_TOKEN_DESCRIPTION);
        self::assertSame('settings_access_token_name', Moodle\Infrastructure\Internationalization::SETTINGS_ACCESS_TOKEN_NAME);
        self::assertSame('settings_element_url_description', Moodle\Infrastructure\Internationalization::SETTINGS_ELEMENT_URL_DESCRIPTION);
        self::assertSame('settings_element_url_name', Moodle\Infrastructure\Internationalization::SETTINGS_ELEMENT_URL_NAME);
        self::assertSame('settings_homeserver_heading', Moodle\Infrastructure\Internationalization::SETTINGS_HOMESERVER_HEADING);
        self::assertSame('settings_homeserver_url_description', Moodle\Infrastructure\Internationalization::SETTINGS_HOMESERVER_URL_DESCRIPTION);
        self::assertSame('settings_homeserver_url_name', Moodle\Infrastructure\Internationalization::SETTINGS_HOMESERVER_URL_NAME);
        self::assertSame('view_alert_many_rooms', Moodle\Infrastructure\Internationalization::VIEW_ALERT_MANY_ROOMS);
        self::assertSame('view_button_join_room', Moodle\Infrastructure\Internationalization::VIEW_BUTTON_JOIN_ROOM);
        self::assertSame('view_error_no_groups', Moodle\Infrastructure\Internationalization::VIEW_ERROR_NO_GROUPS);
        self::assertSame('view_error_no_room_in_group', Moodle\Infrastructure\Internationalization::VIEW_ERROR_NO_ROOM_IN_GROUP);
        self::assertSame('view_error_no_rooms', Moodle\Infrastructure\Internationalization::VIEW_ERROR_NO_ROOMS);
        self::assertSame('view_error_no_visible_groups', Moodle\Infrastructure\Internationalization::VIEW_ERROR_NO_VISIBLE_GROUPS);
    }
}
