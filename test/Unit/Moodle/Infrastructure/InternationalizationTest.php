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
        self::assertSame('action_edit_matrix_user_id_header', Moodle\Infrastructure\Internationalization::ACTION_EDIT_MATRIX_USER_ID_HEADER);
        self::assertSame('action_edit_matrix_user_id_warning_no_matrix_user_id', Moodle\Infrastructure\Internationalization::ACTION_EDIT_MATRIX_USER_ID_WARNING_NO_MATRIX_USER_ID);
        self::assertSame('action_list_rooms_header', Moodle\Infrastructure\Internationalization::ACTION_LIST_ROOMS_HEADER);
        self::assertSame('action_list_rooms_warning_no_rooms', Moodle\Infrastructure\Internationalization::ACTION_LIST_ROOMS_WARNING_NO_ROOMS);
        self::assertSame('form_edit_matrix_user_id_error_matrix_user_id_invalid', Moodle\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_ERROR_MATRIX_USER_ID_INVALID);
        self::assertSame('form_edit_matrix_user_id_error_matrix_user_id_required', Moodle\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_ERROR_MATRIX_USER_ID_REQUIRED);
        self::assertSame('form_edit_matrix_user_id_header', Moodle\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_HEADER);
        self::assertSame('form_edit_matrix_user_id_matrix_user_id_name', Moodle\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_MATRIX_USER_ID_NAME);
        self::assertSame('form_edit_matrix_user_id_matrix_user_id_name_help', Moodle\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_MATRIX_USER_ID_NAME_HELP);
        self::assertSame('mod_form_basic_settings_header', Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_HEADER);
        self::assertSame('mod_form_basic_settings_name', Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME);
        self::assertSame('mod_form_basic_settings_name_default', Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_DEFAULT);
        self::assertSame('mod_form_basic_settings_name_error_maxlength', Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_ERROR_MAXLENGTH);
        self::assertSame('mod_form_basic_settings_name_error_required', Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_ERROR_REQUIRED);
        self::assertSame('mod_form_basic_settings_name_help', Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_HELP);
        self::assertSame('mod_form_basic_settings_name_name', Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_NAME);
        self::assertSame('mod_form_basic_settings_target_error_required', Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TARGET_ERROR_REQUIRED);
        self::assertSame('mod_form_basic_settings_target_label_element_url', Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TARGET_LABEL_ELEMENT_URL);
        self::assertSame('mod_form_basic_settings_target_label_matrix_to', Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TARGET_LABEL_MATRIX_TO);
        self::assertSame('mod_form_basic_settings_target_name', Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TARGET_NAME);
        self::assertSame('mod_form_basic_settings_topic_help', Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TOPIC_HELP);
        self::assertSame('mod_form_basic_settings_topic_name', Moodle\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TOPIC_NAME);
        self::assertSame('settings_access_token_description', Moodle\Infrastructure\Internationalization::SETTINGS_ACCESS_TOKEN_DESCRIPTION);
        self::assertSame('settings_access_token_name', Moodle\Infrastructure\Internationalization::SETTINGS_ACCESS_TOKEN_NAME);
        self::assertSame('settings_element_url_description', Moodle\Infrastructure\Internationalization::SETTINGS_ELEMENT_URL_DESCRIPTION);
        self::assertSame('settings_element_url_name', Moodle\Infrastructure\Internationalization::SETTINGS_ELEMENT_URL_NAME);
        self::assertSame('settings_homeserver_heading', Moodle\Infrastructure\Internationalization::SETTINGS_HOMESERVER_HEADING);
        self::assertSame('settings_homeserver_url_description', Moodle\Infrastructure\Internationalization::SETTINGS_HOMESERVER_URL_DESCRIPTION);
        self::assertSame('settings_homeserver_url_name', Moodle\Infrastructure\Internationalization::SETTINGS_HOMESERVER_URL_NAME);
    }
}
