<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

use core\event;
use mod_matrix\Matrix;

\defined('MOODLE_INTERNAL') || exit();

$observers = (static function (): array {
    $map = [
        event\group_created::class => [
            Matrix\Infrastructure\Observer::class,
            'onGroupCreated',
        ],
        event\group_member_added::class => [
            Matrix\Infrastructure\Observer::class,
            'onGroupMemberChange',
        ],
        event\group_member_removed::class => [
            Matrix\Infrastructure\Observer::class,
            'onGroupMemberChange',
        ],
        event\role_assigned::class => [
            Matrix\Infrastructure\Observer::class,
            'onRoleChanged',
        ],
        event\role_capabilities_updated::class => [
            Matrix\Infrastructure\Observer::class,
            'onRoleChanged',
        ],
        event\role_deleted::class => [
            Matrix\Infrastructure\Observer::class,
            'onRoleChanged',
        ],
        event\role_unassigned::class => [
            Matrix\Infrastructure\Observer::class,
            'onRoleChanged',
        ],
        event\user_enrolment_created::class => [
            Matrix\Infrastructure\Observer::class,
            'onUserEnrolmentChanged',
        ],
        event\user_enrolment_deleted::class => [
            Matrix\Infrastructure\Observer::class,
            'onUserEnrolmentChanged',
        ],
        event\user_enrolment_updated::class => [
            Matrix\Infrastructure\Observer::class,
            'onUserEnrolmentChanged',
        ],
    ];

    return \array_map(static function (string $event, array $callback): array {
        return [
            'callback' => $callback,
            'eventname' => $event,
            'internal' => false,
        ];
    }, \array_keys($map), \array_values($map));
})();
