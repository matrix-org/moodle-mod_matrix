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
            Matrix\Infrastructure\EventSubscriber::class,
            'onGroupCreated',
        ],
        event\group_member_added::class => [
            Matrix\Infrastructure\EventSubscriber::class,
            'onGroupMemberAdded',
        ],
        event\group_member_removed::class => [
            Matrix\Infrastructure\EventSubscriber::class,
            'onGroupMemberRemoved',
        ],
        event\role_assigned::class => [
            Matrix\Infrastructure\EventSubscriber::class,
            'onRoleAssigned',
        ],
        event\role_capabilities_updated::class => [
            Matrix\Infrastructure\EventSubscriber::class,
            'onRoleCapabilitiesUpdated',
        ],
        event\role_deleted::class => [
            Matrix\Infrastructure\EventSubscriber::class,
            'onRoleDeleted',
        ],
        event\role_unassigned::class => [
            Matrix\Infrastructure\EventSubscriber::class,
            'onRoleUnassigned',
        ],
        event\user_enrolment_created::class => [
            Matrix\Infrastructure\EventSubscriber::class,
            'onUserEnrolmentCreated',
        ],
        event\user_enrolment_deleted::class => [
            Matrix\Infrastructure\EventSubscriber::class,
            'onUserEnrolmentDeleted',
        ],
        event\user_enrolment_updated::class => [
            Matrix\Infrastructure\EventSubscriber::class,
            'onUserEnrolmentUpdated',
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
