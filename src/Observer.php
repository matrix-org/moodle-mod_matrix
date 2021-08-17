<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix;

use core\event;

defined('MOODLE_INTERNAL') || exit;

class Observer
{
    public static function onGroupMemberChange($event): void
    {
        $container = Container::instance();

        $moduleRepository = $container->moduleRepository();

        $modules = $moduleRepository->findAllBy([
            'course' => $event->courseid,
        ]);

        if ([] === $modules) {
            return;
        }

        $service = $container->service();

        $service->synchronizeRoomMembers(
            $event->courseid,
            $event->objectid
        );
    }

    public static function onGroupCreated(event\group_created $event): void
    {
        $container = Container::instance();

        $moduleRepository = $container->moduleRepository();

        $modules = $moduleRepository->findAllBy([
            'course' => $event->courseid,
        ]);

        if ([] === $modules) {
            return;
        }

        $service = $container->service();

        $service->prepareRoomForGroup(
            $event->courseid,
            $event->objectid
        );
    }

    public static function onRoleChanged(): void
    {
        $service = Container::instance()->service();

        $service->resync_all(null); // ALL the rooms
    }

    public static function onUserEnrolmentChanged($event): void
    {
        $service = Container::instance()->service();

        $service->resync_all($event->courseid);
    }
}
