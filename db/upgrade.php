<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

\defined('MOODLE_INTERNAL') || exit();

use mod_matrix\Container;
use mod_matrix\Moodle;

/**
 * @see https://docs.moodle.org/dev/Upgrade_API
 * @see https://github.com/moodle/moodle/blob/v3.9.5/lib/upgradelib.php#L688-L693
 */
function xmldb_matrix_upgrade(int $oldversion = 0): bool
{
    global $DB;

    $dbman = $DB->get_manager();

    if (2020110901 > $oldversion) {
        $table = new xmldb_table(Moodle\Infrastructure\DatabaseBasedModuleRepository::TABLE);

        $field = new xmldb_field('name');
        $field->set_attributes(XMLDB_TYPE_CHAR, 255, false, true, false, 'Matrix Chat');
        $dbman->add_field($table, $field);

        $field = new xmldb_field('type');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 2, false, true, false, 0);
        $dbman->add_field($table, $field);

        upgrade_mod_savepoint(
            true,
            2020110901,
            Moodle\Application\Plugin::NAME,
        );
    }

    if (2020110948 > $oldversion) {
        $table = new xmldb_table(Moodle\Infrastructure\DatabaseBasedRoomRepository::TABLE);

        $field = new xmldb_field('course');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 10, true, false, false, null);
        $dbman->rename_field($table, $field, 'course_id');

        $field = new xmldb_field('group');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 10, true, false, false, null);
        $dbman->rename_field($table, $field, 'group_id');

        upgrade_mod_savepoint(
            true,
            2020110948,
            Moodle\Application\Plugin::NAME,
        );
    }

    if (2021091300 > $oldversion) {
        $table = new xmldb_table(Moodle\Infrastructure\DatabaseBasedModuleRepository::TABLE);

        $dbman->add_field(
            $table,
            new xmldb_field(
                'section',
                XMLDB_TYPE_INTEGER,
                10,
                true,
                true,
                false,
                0,
                'course',
            ),
        );

        upgrade_mod_savepoint(
            true,
            2021091300,
            Moodle\Application\Plugin::NAME,
        );
    }

    if (2021091400 > $oldversion) {
        $DB->delete_records(Moodle\Infrastructure\DatabaseBasedRoomRepository::TABLE);

        $table = new xmldb_table(Moodle\Infrastructure\DatabaseBasedRoomRepository::TABLE);

        $dbman->add_field(
            $table,
            new xmldb_field(
                'module_id',
                XMLDB_TYPE_INTEGER,
                10,
                true,
                true,
                false,
                0,
                'course_id',
            ),
        );

        $dbman->drop_field(
            $table,
            new xmldb_field(
                'course_id',
                XMLDB_TYPE_INTEGER,
                10,
                true,
                false,
                false,
                null,
            ),
        );

        upgrade_mod_savepoint(
            true,
            2021091400,
            Moodle\Application\Plugin::NAME,
        );
    }

    if (2021120600 > $oldversion) {
        $table = new xmldb_table(Moodle\Infrastructure\DatabaseBasedModuleRepository::TABLE);

        $dbman->add_field(
            $table,
            new xmldb_field(
                'topic',
                XMLDB_TYPE_TEXT,
                null,
                true,
                false,
                false,
                null,
                'name',
            ),
        );

        upgrade_mod_savepoint(
            true,
            2021120600,
            Moodle\Application\Plugin::NAME,
        );
    }

    if (2021120700 > $oldversion) {
        $table = new xmldb_table(Moodle\Infrastructure\DatabaseBasedModuleRepository::TABLE);

        $dbman->add_field(
            $table,
            new xmldb_field(
                'target',
                XMLDB_TYPE_CHAR,
                16,
                true,
                true,
                false,
                Moodle\Domain\ModuleTarget::elementUrl()->toString(),
                'topic',
            ),
        );

        upgrade_mod_savepoint(
            true,
            2021120700,
            Moodle\Application\Plugin::NAME,
        );
    }

    if (2022011100 > $oldversion) {
        $container = Container::instance();

        $oldTarget = Moodle\Domain\ModuleTarget::matrixTo();
        $newTarget = Moodle\Domain\ModuleTarget::matrixTo();

        $configuration = $container->configuration();

        if ($configuration->elementUrl() !== '') {
            $newTarget = Moodle\Domain\ModuleTarget::elementUrl();
        }

        if (!$newTarget->equals($oldTarget)) {
            $sql = \sprintf(
                <<<'SQL'
UPDATE `%s` SET `target` = :new_target WHERE `target` = :old_target
SQL,
                \sprintf(
                    '%s%s',
                    $DB->get_prefix(),
                    Moodle\Infrastructure\DatabaseBasedModuleRepository::TABLE,
                ),
            );

            $DB->execute($sql, [
                'new_target' => $newTarget->toString(),
                'old_target' => $oldTarget->toString(),
            ]);
        }

        upgrade_mod_savepoint(
            true,
            2022011100,
            Moodle\Application\Plugin::NAME,
        );
    }

    return true;
}
