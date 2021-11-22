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
        'callback' => '\mod_matrix\Observer::onGroupMemberChange',
        'eventname' => event\group_member_added::class,
        'internal' => false,
    ],
    [
        'callback' => '\mod_matrix\Observer::onGroupMemberChange',
        'eventname' => event\group_member_removed::class,
        'internal' => false,
    ],
    [
        'callback' => '\mod_matrix\Observer::onGroupCreated',
        'eventname' => event\group_created::class,
        'internal' => false,
    ],
    [
        'callback' => '\mod_matrix\Observer::onRoleChanged',
        'eventname' => event\role_assigned::class,
        'internal' => false,
    ],
    [
        'callback' => '\mod_matrix\Observer::onRoleChanged',
        'eventname' => event\role_unassigned::class,
        'internal' => false,
    ],
    [
        'callback' => '\mod_matrix\Observer::onRoleChanged',
        'eventname' => event\role_capabilities_updated::class,
        'internal' => false,
    ],
    [
        'callback' => '\mod_matrix\Observer::onRoleChanged',
        'eventname' => event\role_deleted::class,
        'internal' => false,
    ],
    [
        'callback' => '\mod_matrix\Observer::onUserEnrolmentChanged',
        'eventname' => event\user_enrolment_created::class,
        'internal' => false,
    ],
    [
        'callback' => '\mod_matrix\Observer::onUserEnrolmentChanged',
        'eventname' => event\user_enrolment_deleted::class,
        'internal' => false,
    ],
    [
        'callback' => '\mod_matrix\Observer::onUserEnrolmentChanged',
        'eventname' => event\user_enrolment_updated::class,
        'internal' => false,
    ],
];
