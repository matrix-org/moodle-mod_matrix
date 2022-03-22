<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

\defined('MOODLE_INTERNAL') || exit();

use mod_matrix\Moodle;
use mod_matrix\Plugin;

/**
 * @see https://github.com/moodle/moodle/blob/v3.9.5/lib/adminlib.php#L183-L191
 */
function xmldb_matrix_uninstall(): void
{
    global $DB;

    require_once __DIR__ . '/../vendor/autoload.php';

    Plugin\Infrastructure\Installer::uninstall($DB);
}
