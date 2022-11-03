<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

\defined('MOODLE_INTERNAL') || exit();

/**
 * @see https://docs.moodle.org/dev/Plugin_files#db.2Faccess.php
 * @see https://docs.moodle.org/dev/Access_API
 */
$capabilities = [
    // Ability to add a new instance.
    'mod/matrix:addinstance' => [
        'archetypes' => [
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ],
        'captype' => 'write',
        'clonepermissionsfrom' => 'moodle/course:manageactivities',
        'contextlevel' => CONTEXT_COURSE,
        'riskbitmask' => RISK_XSS,
    ],

    // Ability to access instances
    'mod/matrix:view' => [
        'archetypes' => [
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
        ],
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
    ],

    // Identifies staff to the membership sync
    'mod/matrix:staff' => [
        'archetypes' => [
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
        ],
        'captype' => 'read',
        'clonepermissionsfrom' => 'moodle/assign:grade',
        'contextlevel' => CONTEXT_SYSTEM,
    ],
];
