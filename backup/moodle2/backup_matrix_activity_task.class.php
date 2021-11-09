<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

\defined('MOODLE_INTERNAL') || exit();

class backup_matrix_activity_task extends backup_activity_task
{
    public static function encode_content_links($content): void
    {
        // Nothing relevant
    }

    protected function define_my_settings(): void
    {
        // Nothing relevant
    }

    protected function define_my_steps(): void
    {
        // TODO
    }
}
