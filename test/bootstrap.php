<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

require_once __DIR__ . '/../vendor/autoload.php';

$CFG = new stdClass();

$CFG->admin = 'admin';
$CFG->cachedir = __DIR__ . '/../.build/moodle';
$CFG->dirroot = __DIR__ . '/../vendor/moodle/moodle';
$CFG->libdir = __DIR__ . '/../vendor/moodle/moodle/lib';

/**
 * @see https://github.com/moodle/moodle/blob/v3.9.5/lib/setup.php#L278-L280
 */
\define('CACHE_DISABLE_ALL', true);

/**
 * @see https://github.com/moodle/moodle/blob/v3.9.5/lib/setup.php#L398-L401https://github.com/moodle/moodle/blob/v3.9.5/lib/setup.php#L398-L401
 */
\define('MOODLE_INTERNAL', true);

/**
 * @see https://github.com/moodle/moodle/blob/v3.9.5/lib/setup.php#L404
 */
require_once __DIR__ . '/../vendor/moodle/moodle/lib/classes/component.php';

/**
 * @see https://github.com/moodle/moodle/blob/v3.9.5/lib/setup.php#L540
 */
require_once __DIR__ . '/../vendor/moodle/moodle/lib/setuplib.php';

/**
 * @see https://github.com/moodle/moodle/blob/v3.9.5/lib/setup.php#L593
 */
\spl_autoload_register('core_component::classloader');

/**
 * @see https://github.com/moodle/moodle/blob/v3.9.5/lib/setup.php#L600-L618
 */
require_once __DIR__ . '/../vendor/moodle/moodle/lib/filterlib.php';       // Functions for filtering test as it is output

require_once __DIR__ . '/../vendor/moodle/moodle/lib/ajax/ajaxlib.php';    // Functions for managing our use of JavaScript and YUI

require_once __DIR__ . '/../vendor/moodle/moodle/lib/weblib.php';          // Functions relating to HTTP and content

require_once __DIR__ . '/../vendor/moodle/moodle/lib/outputlib.php';       // Functions for generating output

require_once __DIR__ . '/../vendor/moodle/moodle/lib/navigationlib.php';   // Class for generating Navigation structure

require_once __DIR__ . '/../vendor/moodle/moodle/lib/dmllib.php';          // Database access

require_once __DIR__ . '/../vendor/moodle/moodle/lib/datalib.php';         // Legacy lib with a big-mix of functions.

require_once __DIR__ . '/../vendor/moodle/moodle/lib/accesslib.php';       // Access control functions

require_once __DIR__ . '/../vendor/moodle/moodle/lib/deprecatedlib.php';   // Deprecated functions included for backward compatibility

require_once __DIR__ . '/../vendor/moodle/moodle/lib/moodlelib.php';       // Other general-purpose functions

require_once __DIR__ . '/../vendor/moodle/moodle/lib/enrollib.php';        // Enrolment related functions

require_once __DIR__ . '/../vendor/moodle/moodle/lib/pagelib.php';         // Library that defines the moodle_page class, used for $PAGE

require_once __DIR__ . '/../vendor/moodle/moodle/lib/blocklib.php';        // Library for controlling blocks

require_once __DIR__ . '/../vendor/moodle/moodle/lib/grouplib.php';        // Groups functions

require_once __DIR__ . '/../vendor/moodle/moodle/lib/sessionlib.php';      // All session and cookie related stuff

require_once __DIR__ . '/../vendor/moodle/moodle/lib/editorlib.php';       // All text editor related functions and classes

require_once __DIR__ . '/../vendor/moodle/moodle/lib/messagelib.php';      // Messagelib functions

require_once __DIR__ . '/../vendor/moodle/moodle/lib/modinfolib.php';      // Cached information on course-module instances

require_once __DIR__ . '/../vendor/moodle/moodle/cache/lib.php';       // Cache API
