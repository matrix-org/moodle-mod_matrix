<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Plugin\Infrastructure;

final class Internationalization
{
    /**
     * @see \mod_matrix\Plugin\Infrastructure\Action\EditMatrixUserIdAction
     */
    public const ACTION_EDIT_MATRIX_USER_ID_HEADER = 'action_edit_matrix_user_id_header';
    public const ACTION_EDIT_MATRIX_USER_ID_INFO_SUGGESTION = 'action_edit_matrix_user_id_info_suggestion';
    public const ACTION_EDIT_MATRIX_USER_ID_WARNING_NO_MATRIX_USER_ID = 'action_edit_matrix_user_id_warning_no_matrix_user_id';

    /**
     * @see \mod_matrix\Plugin\Infrastructure\Action\ListRoomsAction
     */
    public const ACTION_LIST_ROOMS_HEADER = 'action_list_rooms_header';
    public const ACTION_LIST_ROOMS_WARNING_NO_ROOMS = 'action_list_rooms_warning_no_rooms';

    /**
     * @see \mod_matrix\Plugin\Infrastructure\Form\EditMatrixUserIdForm
     */
    public const FORM_EDIT_MATRIX_USER_ID_ERROR_MATRIX_USER_ID_INVALID = 'form_edit_matrix_user_id_error_matrix_user_id_invalid';
    public const FORM_EDIT_MATRIX_USER_ID_ERROR_MATRIX_USER_ID_REQUIRED = 'form_edit_matrix_user_id_error_matrix_user_id_required';
    public const FORM_EDIT_MATRIX_USER_ID_HEADER = 'form_edit_matrix_user_id_header';
    public const FORM_EDIT_MATRIX_USER_ID_MATRIX_USER_ID_NAME = 'form_edit_matrix_user_id_matrix_user_id_name';
    public const FORM_EDIT_MATRIX_USER_ID_MATRIX_USER_ID_NAME_HELP = 'form_edit_matrix_user_id_matrix_user_id_name_help';
    public const FORM_EDIT_MATRIX_USER_ID_MATRIX_USER_ID_NAME_SUGGESTION = 'form_edit_matrix_user_id_matrix_user_id_name_suggestion';
    public const FORM_EDIT_MATRIX_USER_ID_MATRIX_USER_ID_NAME_SUGGESTION_DEFAULT = 'form_edit_matrix_user_id_matrix_user_id_name_suggestion_default';
    public const FORM_EDIT_MATRIX_USER_ID_MATRIX_USER_ID_NAME_SUGGESTION_HELP = 'form_edit_matrix_user_id_matrix_user_id_name_suggestion_help';

    /**
     * @see \mod_matrix_mod_form
     */
    public const MOD_FORM_BASIC_SETTINGS_HEADER = 'mod_form_basic_settings_header';
    public const MOD_FORM_BASIC_SETTINGS_NAME = 'mod_form_basic_settings_name';
    public const MOD_FORM_BASIC_SETTINGS_NAME_DEFAULT = 'mod_form_basic_settings_name_default';
    public const MOD_FORM_BASIC_SETTINGS_NAME_ERROR_MAXLENGTH = 'mod_form_basic_settings_name_error_maxlength';
    public const MOD_FORM_BASIC_SETTINGS_NAME_ERROR_REQUIRED = 'mod_form_basic_settings_name_error_required';
    public const MOD_FORM_BASIC_SETTINGS_NAME_HELP = 'mod_form_basic_settings_name_help';
    public const MOD_FORM_BASIC_SETTINGS_NAME_NAME = 'mod_form_basic_settings_name_name';
    public const MOD_FORM_BASIC_SETTINGS_TARGET_ERROR_REQUIRED = 'mod_form_basic_settings_target_error_required';
    public const MOD_FORM_BASIC_SETTINGS_TARGET_LABEL_ELEMENT_URL = 'mod_form_basic_settings_target_label_element_url';
    public const MOD_FORM_BASIC_SETTINGS_TARGET_LABEL_MATRIX_TO = 'mod_form_basic_settings_target_label_matrix_to';
    public const MOD_FORM_BASIC_SETTINGS_TARGET_NAME = 'mod_form_basic_settings_target_name';
    public const MOD_FORM_BASIC_SETTINGS_TOPIC_HELP = 'mod_form_basic_settings_topic_help';
    public const MOD_FORM_BASIC_SETTINGS_TOPIC_NAME = 'mod_form_basic_settings_topic_name';

    /**
     * @see \mod_matrix\privacy\provider
     */
    public const PRIVACY_METADATA_MATRIX_USER_ID_DATA = 'privacy_metadata_matrix_user_id_data';
    public const PRIVACY_METADATA_MATRIX_USER_ID_DATAFORMAT = 'privacy_metadata_matrix_user_id_dataformat';
    public const PRIVACY_METADATA_MATRIX_USER_ID_EXPLANATION = 'privacy_metadata_matrix_user_id_explanation';
    public const PRIVACY_METADATA_MATRIX_USER_ID_FIELDID = 'privacy_metadata_matrix_user_id_fieldid';
    public const PRIVACY_METADATA_MATRIX_USER_ID_USERID = 'privacy_metadata_matrix_user_id_userid';

    /**
     * @see settings.php
     */
    public const SETTINGS_ACCESS_TOKEN_DESCRIPTION = 'settings_access_token_description';
    public const SETTINGS_ACCESS_TOKEN_NAME = 'settings_access_token_name';
    public const SETTINGS_ELEMENT_URL_DESCRIPTION = 'settings_element_url_description';
    public const SETTINGS_ELEMENT_URL_NAME = 'settings_element_url_name';
    public const SETTINGS_HOMESERVER_HEADING = 'settings_homeserver_heading';
    public const SETTINGS_HOMESERVER_URL_DESCRIPTION = 'settings_homeserver_url_description';
    public const SETTINGS_HOMESERVER_URL_NAME = 'settings_homeserver_url_name';
}
