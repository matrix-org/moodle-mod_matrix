<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

\defined('MOODLE_INTERNAL') || exit();

require_once __DIR__ . '/restore_matrix_activity_structure_step.class.php';

/**
 * @see https://docs.moodle.org/dev/Restore_API
 * @see https://docs.moodle.org/dev/Restore_API#API_for_activity_modules
 * @see https://docs.moodle.org/dev/Restore_2.0_for_developers
 */
final class restore_matrix_activity_task extends restore_activity_task
{
    public static function define_decode_contents(): array
    {
        return [];
    }

    public static function define_decode_rules(): array
    {
        return [
            new restore_decode_rule(
                'MATRIXINDEX',
                '/mod/matrix/index.php?id=$1',
                'course',
            ),
            new restore_decode_rule(
                'MATRIXVIEW',
                '/mod/matrix/view.php?id=$1',
                'course_module',
            ),
        ];
    }

    public static function define_restore_log_rules(): array
    {
        return [];
    }

    protected function define_my_settings(): void
    {
    }

    protected function define_my_steps(): void
    {
        $this->add_step(new restore_matrix_activity_structure_step(
            'matrix',
            'matrix.xml',
        ));
    }
}
