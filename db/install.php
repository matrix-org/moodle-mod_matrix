<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

\defined('MOODLE_INTERNAL') || exit();

use mod_matrix\Moodle;

/**
 * @see https://github.com/moodle/moodle/blob/v3.9.5/lib/upgradelib.php#L661-L667
 */
function xmldb_matrix_install(): void
{
    global $DB;

    Moodle\Infrastructure\Installer::install($DB);
}
