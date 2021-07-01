<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || exit();

global $CFG;

require_once __DIR__ . '/lib.php';

/** @var MATRIX_DEFAULT_SERVER_URL string of the default client-server API URL */
const MATRIX_DEFAULT_SERVER_URL = 'https://matrix-client.matrix.org';

/** @var MATRIX_DEFAULT_ACCESS_TOKEN string of the default access token on the given homeserver */
const MATRIX_DEFAULT_ACCESS_TOKEN = '';

/** @var MATRIX_DEFAULT_ELEMENT_URL string of the default Element Web URL */
const MATRIX_DEFAULT_ELEMENT_URL = '';
