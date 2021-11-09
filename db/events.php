<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

use core\event;

\defined('MOODLE_INTERNAL') || exit();

$observers = [
    // Notes:
    // * We use internal:false to listen *after* the DB transaction completes.
    // * We listen to pretty much anything that will affect our state of the Matrix rooms.
    // * If anything in this file changes, bump the version number.

    [
        'eventname' => event\group_member_added::class,
        'callback' => '\mod_matrix\observer::onGroupMemberChange',
        'internal' => false,
    ],
    [
        'eventname' => event\group_member_removed::class,
        'callback' => '\mod_matrix\observer::onGroupMemberChange',
        'internal' => false,
    ],
    [
        'eventname' => event\group_created::class,
        'callback' => '\mod_matrix\observer::onGroupCreated',
        'internal' => false,
    ],
    [
        'eventname' => event\role_assigned::class,
        'callback' => '\mod_matrix\observer::onRoleChanged',
        'internal' => false,
    ],
    [
        'eventname' => event\role_unassigned::class,
        'callback' => '\mod_matrix\observer::onRoleChanged',
        'internal' => false,
    ],
    [
        'eventname' => event\role_capabilities_updated::class,
        'callback' => '\mod_matrix\observer::onRoleChanged',
        'internal' => false,
    ],
    [
        'eventname' => event\role_deleted::class,
        'callback' => '\mod_matrix\observer::onRoleChanged',
        'internal' => false,
    ],
    [
        'eventname' => event\user_enrolment_created::class,
        'callback' => '\mod_matrix\observer::onUserEnrolmentChanged',
        'internal' => false,
    ],
    [
        'eventname' => event\user_enrolment_deleted::class,
        'callback' => '\mod_matrix\observer::onUserEnrolmentChanged',
        'internal' => false,
    ],
    [
        'eventname' => event\user_enrolment_updated::class,
        'callback' => '\mod_matrix\observer::onUserEnrolmentChanged',
        'internal' => false,
    ],
];
