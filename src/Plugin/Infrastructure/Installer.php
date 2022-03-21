<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Plugin\Infrastructure;

use mod_matrix\Plugin;

final class Installer
{
    public static function install(\moodle_database $DB): void
    {
        self::createMatrixUserIdProfileField($DB);
    }

    private static function createMatrixUserIdProfileField(\moodle_database $DB): void
    {
        $matrixUserIdProfileFieldExists = $DB->record_exists('user_info_field', [
            'shortname' => Plugin\Infrastructure\MoodleFunctionBasedMatrixUserIdLoader::USER_PROFILE_FIELD_NAME,
        ]);

        if ($matrixUserIdProfileFieldExists) {
            return;
        }

        $data = [
            'categoryid' => 1,
            'datatype' => 'text',
            'defaultdata' => '',
            'defaultdataformat' => 0,
            'description' => 'A valid matrix user identifier, e.g., @user:example.org.',
            'descriptionformat' => 1,
            'forceunique' => 1,
            'locked' => 0,
            'name' => 'Matrix User Id',
            'param1' => '30',
            'param2' => '255',
            'param3' => '0',
            'required' => 0,
            'shortname' => Plugin\Infrastructure\MoodleFunctionBasedMatrixUserIdLoader::USER_PROFILE_FIELD_NAME,
            'signup' => 0,
            'sortorder' => 1,
            'visible' => 2,
        ];

        $DB->insert_record(
            'user_info_field',
            (object) $data,
        );
    }
}
