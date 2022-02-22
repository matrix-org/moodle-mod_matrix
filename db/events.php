<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

use mod_matrix\observer;

\defined('MOODLE_INTERNAL') || exit();

/**
 * @see https://docs.moodle.org/dev/Plugin_files#db.2Fevents.php
 * @see https://docs.moodle.org/dev/Events_API
 */
$observers = observer::observers();
