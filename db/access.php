<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

\defined('MOODLE_INTERNAL') || exit();

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
