<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Test\Unit\Plugin\Infrastructure;

use mod_matrix\Plugin;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Plugin\Infrastructure\Internationalization
 */
final class InternationalizationTest extends Framework\TestCase
{
    public function testConstants(): void
    {
        self::assertSame('action_edit_matrix_user_id_header', Plugin\Infrastructure\Internationalization::ACTION_EDIT_MATRIX_USER_ID_HEADER);
        self::assertSame('action_edit_matrix_user_id_info_suggestion', Plugin\Infrastructure\Internationalization::ACTION_EDIT_MATRIX_USER_ID_INFO_SUGGESTION);
        self::assertSame('action_edit_matrix_user_id_warning_no_matrix_user_id', Plugin\Infrastructure\Internationalization::ACTION_EDIT_MATRIX_USER_ID_WARNING_NO_MATRIX_USER_ID);
        self::assertSame('action_list_rooms_header', Plugin\Infrastructure\Internationalization::ACTION_LIST_ROOMS_HEADER);
        self::assertSame('action_list_rooms_warning_no_rooms', Plugin\Infrastructure\Internationalization::ACTION_LIST_ROOMS_WARNING_NO_ROOMS);
        self::assertSame('form_edit_matrix_user_id_error_matrix_user_id_invalid', Plugin\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_ERROR_MATRIX_USER_ID_INVALID);
        self::assertSame('form_edit_matrix_user_id_error_matrix_user_id_required', Plugin\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_ERROR_MATRIX_USER_ID_REQUIRED);
        self::assertSame('form_edit_matrix_user_id_header', Plugin\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_HEADER);
        self::assertSame('form_edit_matrix_user_id_matrix_user_id_name', Plugin\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_MATRIX_USER_ID_NAME);
        self::assertSame('form_edit_matrix_user_id_matrix_user_id_name_help', Plugin\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_MATRIX_USER_ID_NAME_HELP);
        self::assertSame('form_edit_matrix_user_id_matrix_user_id_name_suggestion', Plugin\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_MATRIX_USER_ID_NAME_SUGGESTION);
        self::assertSame('form_edit_matrix_user_id_matrix_user_id_name_suggestion_default', Plugin\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_MATRIX_USER_ID_NAME_SUGGESTION_DEFAULT);
        self::assertSame('form_edit_matrix_user_id_matrix_user_id_name_suggestion_help', Plugin\Infrastructure\Internationalization::FORM_EDIT_MATRIX_USER_ID_MATRIX_USER_ID_NAME_SUGGESTION_HELP);
        self::assertSame('mod_form_basic_settings_header', Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_HEADER);
        self::assertSame('mod_form_basic_settings_name', Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME);
        self::assertSame('mod_form_basic_settings_name_default', Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_DEFAULT);
        self::assertSame('mod_form_basic_settings_name_error_maxlength', Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_ERROR_MAXLENGTH);
        self::assertSame('mod_form_basic_settings_name_error_required', Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_ERROR_REQUIRED);
        self::assertSame('mod_form_basic_settings_name_help', Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_HELP);
        self::assertSame('mod_form_basic_settings_name_name', Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_NAME_NAME);
        self::assertSame('mod_form_basic_settings_target_error_required', Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TARGET_ERROR_REQUIRED);
        self::assertSame('mod_form_basic_settings_target_label_element_url', Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TARGET_LABEL_ELEMENT_URL);
        self::assertSame('mod_form_basic_settings_target_label_matrix_to', Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TARGET_LABEL_MATRIX_TO);
        self::assertSame('mod_form_basic_settings_target_name', Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TARGET_NAME);
        self::assertSame('mod_form_basic_settings_topic_help', Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TOPIC_HELP);
        self::assertSame('mod_form_basic_settings_topic_name', Plugin\Infrastructure\Internationalization::MOD_FORM_BASIC_SETTINGS_TOPIC_NAME);
        self::assertSame('privacy_metadata_matrix_user_id_data', Plugin\Infrastructure\Internationalization::PRIVACY_METADATA_MATRIX_USER_ID_DATA);
        self::assertSame('privacy_metadata_matrix_user_id_dataformat', Plugin\Infrastructure\Internationalization::PRIVACY_METADATA_MATRIX_USER_ID_DATAFORMAT);
        self::assertSame('privacy_metadata_matrix_user_id_explanation', Plugin\Infrastructure\Internationalization::PRIVACY_METADATA_MATRIX_USER_ID_EXPLANATION);
        self::assertSame('privacy_metadata_matrix_user_id_fieldid', Plugin\Infrastructure\Internationalization::PRIVACY_METADATA_MATRIX_USER_ID_FIELDID);
        self::assertSame('privacy_metadata_matrix_user_id_userid', Plugin\Infrastructure\Internationalization::PRIVACY_METADATA_MATRIX_USER_ID_USERID);
        self::assertSame('settings_access_token_description', Plugin\Infrastructure\Internationalization::SETTINGS_ACCESS_TOKEN_DESCRIPTION);
        self::assertSame('settings_access_token_name', Plugin\Infrastructure\Internationalization::SETTINGS_ACCESS_TOKEN_NAME);
        self::assertSame('settings_element_url_description', Plugin\Infrastructure\Internationalization::SETTINGS_ELEMENT_URL_DESCRIPTION);
        self::assertSame('settings_element_url_name', Plugin\Infrastructure\Internationalization::SETTINGS_ELEMENT_URL_NAME);
        self::assertSame('settings_homeserver_heading', Plugin\Infrastructure\Internationalization::SETTINGS_HOMESERVER_HEADING);
        self::assertSame('settings_homeserver_url_description', Plugin\Infrastructure\Internationalization::SETTINGS_HOMESERVER_URL_DESCRIPTION);
        self::assertSame('settings_homeserver_url_name', Plugin\Infrastructure\Internationalization::SETTINGS_HOMESERVER_URL_NAME);
    }
}
