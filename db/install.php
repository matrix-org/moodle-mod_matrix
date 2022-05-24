<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

\defined('MOODLE_INTERNAL') || exit();

use mod_matrix\Moodle;
use mod_matrix\Plugin;

/**
 * @see https://github.com/moodle/moodle/blob/v3.9.5/lib/upgradelib.php#L661-L667
 */
function xmldb_matrix_install(): void
{
    global $DB;

    require_once __DIR__ . '/../vendor/autoload.php';

    Plugin\Infrastructure\Installer::install($DB);
}
