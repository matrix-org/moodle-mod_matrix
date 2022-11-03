<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

use mod_matrix\observer;

\defined('MOODLE_INTERNAL') || exit();

/**
 * @see https://docs.moodle.org/dev/Plugin_files#db.2Fevents.php
 * @see https://docs.moodle.org/dev/Events_API
 */
$observers = observer::observers();
