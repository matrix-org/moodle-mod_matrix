<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\privacy;

\defined('MOODLE_INTERNAL') || exit();

use core_privacy\local;
use mod_matrix\Plugin;

/**
 * @see https://github.com/moodle/moodle/blob/v3.9.5/user/profile/field/text/classes/privacy/provider.php
 */
final class provider implements
    local\metadata\provider,
    local\request\core_userlist_provider,
    local\request\plugin\provider
{
    /**
     * Returns meta data about this system.
     *
     * @param local\metadata\collection $collection the initialised collection to add items to
     *
     * @return local\metadata\collection a listing of user data stored through this system
     */
    public static function get_metadata(local\metadata\collection $collection): local\metadata\collection
    {
        self::requireAutoloader();

        return $collection->add_database_table(
            'user_info_data',
            [
                'data' => Plugin\Infrastructure\Internationalization::PRIVACY_METADATA_MATRIX_USER_ID_DATA,
                'dataformat' => Plugin\Infrastructure\Internationalization::PRIVACY_METADATA_MATRIX_USER_ID_DATAFORMAT,
                'fieldid' => Plugin\Infrastructure\Internationalization::PRIVACY_METADATA_MATRIX_USER_ID_FIELDID,
                'userid' => Plugin\Infrastructure\Internationalization::PRIVACY_METADATA_MATRIX_USER_ID_USERID,
            ],
            Plugin\Infrastructure\Internationalization::PRIVACY_METADATA_MATRIX_USER_ID_EXPLANATION,
        );
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid the user to search
     *
     * @return local\request\contextlist $contextlist  the contextlist containing the list of contexts used in this plugin
     */
    public static function get_contexts_for_userid(int $userid): local\request\contextlist
    {
        self::requireAutoloader();

        $sql = <<<'SQL'
SELECT
    ctx.id
FROM
    {user_info_data} uda
JOIN
    {user_info_field} uif
ON
    uda.fieldid = uif.id
JOIN
    {context} ctx
ON
    ctx.instanceid = uda.userid
AND
    ctx.contextlevel = :contextlevel
WHERE
    uda.userid = :userid
AND
    uif.shortname = :shortname
SQL;

        $contextlist = new local\request\contextlist();

        $contextlist->add_from_sql(
            $sql,
            [
                'contextlevel' => CONTEXT_USER,
                'shortname' => Plugin\Infrastructure\MoodleFunctionBasedMatrixUserIdLoader::USER_PROFILE_FIELD_NAME,
                'userid' => $userid,
            ],
        );

        return $contextlist;
    }

    /**
     * Get the list of users within a specific context.
     *
     * @param local\request\userlist $userlist the userlist containing the list of users who have data in this context/plugin combination
     */
    public static function get_users_in_context(local\request\userlist $userlist): void
    {
        $context = $userlist->get_context();

        if (!$context instanceof \context_user) {
            return;
        }

        self::requireAutoloader();

        $sql = <<<'SQL'
SELECT
    uda.userid
FROM
    {user_info_data} uda
JOIN
    {user_info_field} uif
ON
    uda.fieldid = uif.id
WHERE
    uda.userid = :userid
AND
    uif.shortname = :shortname
SQL;

        $userlist->add_from_sql(
            'userid',
            $sql,
            [
                'userid' => $context->instanceid,
                'shortname' => Plugin\Infrastructure\MoodleFunctionBasedMatrixUserIdLoader::USER_PROFILE_FIELD_NAME,
            ],
        );
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param local\request\approved_contextlist $contextlist the approved contexts to export information for
     */
    public static function export_user_data(local\request\approved_contextlist $contextlist): void
    {
        $user = $contextlist->get_user();

        foreach ($contextlist->get_contexts() as $context) {
            if (CONTEXT_USER !== $context->contextlevel) {
                continue;
            }

            if ($context->instanceid !== $user->id) {
                continue;
            }

            foreach (self::getRecords($user->id) as $result) {
                $data = (object) [
                    'name' => $result->name,
                    'description' => $result->description,
                    'data' => $result->data,
                ];

                local\request\writer::with_context($context)->export_data(
                    [
                        get_string(
                            'pluginname',
                            Plugin\Application\Plugin::NAME,
                        ),
                    ],
                    $data,
                );
            }
        }
    }

    /**
     * Delete all user data which matches the specified context.
     *
     * @param \context $context a user context
     */
    public static function delete_data_for_all_users_in_context(\context $context): void
    {
        if (CONTEXT_USER !== $context->contextlevel) {
            return;
        }

        self::deleteRecords($context->instanceid);
    }

    public static function delete_data_for_users(local\request\approved_userlist $userlist): void
    {
        $context = $userlist->get_context();

        if (!$context instanceof \context_user) {
            return;
        }

        self::deleteRecords($context->instanceid);
    }

    public static function delete_data_for_user(local\request\approved_contextlist $contextlist): void
    {
        $user = $contextlist->get_user();

        foreach ($contextlist->get_contexts() as $context) {
            if (CONTEXT_USER !== $context->contextlevel) {
                continue;
            }

            if ($context->instanceid !== $user->id) {
                continue;
            }

            self::deleteRecords($context->instanceid);
        }
    }

    private static function deleteRecords($userid): void
    {
        global $DB;

        self::requireAutoloader();

        $DB->delete_records_select(
            'user_info_data',
            'fieldid IN (SELECT id FROM {user_info_field} WHERE shortname = :shortname) AND userid = :userid',
            [
                'shortname' => Plugin\Infrastructure\MoodleFunctionBasedMatrixUserIdLoader::USER_PROFILE_FIELD_NAME,
                'userid' => $userid,
            ],
        );
    }

    private static function getRecords($userid): array
    {
        global $DB;

        self::requireAutoloader();

        $sql = <<<'SQL'
SELECT
    *
FROM
    {user_info_data} uda
JOIN
    {user_info_field} uif
ON
    uda.fieldid = uif.id
WHERE
    uda.userid = :userid
AND
    uif.shortname = :shortname
SQL;

        return $DB->get_records_sql(
            $sql,
            [
                'shortname' => Plugin\Infrastructure\MoodleFunctionBasedMatrixUserIdLoader::USER_PROFILE_FIELD_NAME,
                'userid' => $userid,
            ],
        );
    }

    private static function requireAutoloader(): void
    {
        require_once __DIR__ . '/../../vendor/autoload.php';
    }
}
