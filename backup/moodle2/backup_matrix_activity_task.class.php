<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

\defined('MOODLE_INTERNAL') || exit();

require_once __DIR__ . '/backup_matrix_activity_structure_step.class.php';

/**
 * @see https://docs.moodle.org/dev/Backup_API
 * @see https://docs.moodle.org/dev/Backup_API#Backup_task_class
 * @see https://docs.moodle.org/dev/Backup_2.0
 * @see https://docs.moodle.org/dev/Backup_2.0_for_developers
 */
final class backup_matrix_activity_task extends backup_activity_task
{
    public static function encode_content_links($content)
    {
        global $CFG;

        $base = \preg_quote(
            $CFG->wwwroot,
            '/',
        );

        return \preg_replace(
            [
                \sprintf(
                    '/(%s\\/mod\\/matrix\\/index.php\\?id\\=)([0-9]+)/',
                    $base,
                ),
                \sprintf(
                    '/(%s\\/mod\\/matrix\\/view.php\\?id\\=)([0-9]+)/',
                    $base,
                ),
            ],
            [
                '$@MATRIXINDEX*$2@$',
                '$@MATRIXVIEW*$2@$',
            ],
            $content,
        );
    }

    protected function define_my_settings(): void
    {
    }

    protected function define_my_steps(): void
    {
        $this->add_step(new backup_matrix_activity_structure_step(
            'matrix',
            'matrix.xml',
        ));
    }
}
