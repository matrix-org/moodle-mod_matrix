<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

use mod_matrix\Container;
use mod_matrix\Moodle;

defined('MOODLE_INTERNAL') || exit;

require_once __DIR__ . '/vendor/autoload.php';

global $CFG;

/**
 * @see https://github.com/moodle/moodle/blob/v3.9.5/lib/moodlelib.php#L8139-L8175
 *
 * @param string $feature
 *
 * @return null|bool
 */
function matrix_supports($feature)
{
    if (!is_string($feature)) {
        return null;
    }

    $features = [
        FEATURE_BACKUP_MOODLE2 => true,
        FEATURE_COMPLETION_HAS_RULES => true,
        FEATURE_COMPLETION_TRACKS_VIEWS => true,
        FEATURE_GRADE_HAS_GRADE => false,
        FEATURE_GRADE_OUTCOMES => false,
        FEATURE_GROUPINGS => true,
        FEATURE_GROUPS => true,
        FEATURE_IDNUMBER => true,
        FEATURE_MOD_INTRO => true,
        FEATURE_SHOW_DESCRIPTION => true,
    ];

    if (!array_key_exists($feature, $features)) {
        return null;
    }

    return $features[$feature];
}

/**
 * @see https://docs.moodle.org/dev/Activity_modules#lib.php
 * @see https://github.com/moodle/moodle/blob/v3.9.5/course/modlib.php#L126-L131
 *
 * @param object $data
 */
function matrix_add_instance($data)
{
    $container = Container::instance();

    $clock = $container->clock();

    $module = Moodle\Domain\Module::create(
        Moodle\Domain\ModuleId::unknown(),
        Moodle\Domain\Type::fromInt(0),
        Moodle\Domain\Name::fromString(get_string('activity_default_name', 'matrix')),
        Moodle\Domain\CourseId::fromString($data->course),
        Moodle\Domain\SectionId::fromInt($data->section),
        Moodle\Domain\Timestamp::fromInt($clock->now()->getTimestamp()),
        Moodle\Domain\Timestamp::fromInt(0),
    );

    $moduleRepository = $container->moduleRepository();

    $moduleRepository->save($module);

    // Now try to iterate over all the courses and groups and see if any of
    // the rooms need to be created
    $groups = groups_get_all_groups(
        $module->courseId()->toInt(),
        0,
        0,
        'g.*',
        true,
    );

    $service = $container->service();

    if (count($groups) > 0) {
        foreach ($groups as $group) {
            $service->prepareRoomForModuleAndGroup(
                $module,
                Moodle\Domain\GroupId::fromString($group->id),
            );
        }
    } else {
        $service->prepareRoomForModuleAndGroup(
            $module,
            null,
        );
    }

    return $module->id()->toInt();
}

/**
 * @see https://docs.moodle.org/dev/Activity_modules#lib.php
 * @see https://github.com/moodle/moodle/blob/v3.9.5/course/lib.php#L1034-L1040
 * @see https://github.com/moodle/moodle/blob/v3.9.5/course/lib.php#L1054-L1057
 *
 * @param int|string $id
 */
function matrix_delete_instance($id): bool
{
    $container = Container::instance();

    $moduleRepository = $container->moduleRepository();

    $module = $moduleRepository->findOneBy([
        'id' => $id,
    ]);

    if (!$module instanceof Moodle\Domain\Module) {
        return false;
    }

    $roomRepository = $container->roomRepository();

    $rooms = $roomRepository->findAllBy([
        'module_id' => $module->id()->toInt(),
    ]);

    $service = $container->service();

    foreach ($rooms as $room) {
        $service->removeRoom($room);

        $roomRepository->remove($room);
    }

    $moduleRepository->remove($module);

    return true;
}

/**
 * @see https://docs.moodle.org/dev/Activity_modules#lib.php
 * @see https://github.com/moodle/moodle/blob/v3.9.5/course/modlib.php#L611-L614
 */
function matrix_update_instance()
{
    return true; // nothing to do
}

// TODO: Events API
// - Group edits
// - Course enrollment edits
// - Custom field (profile) updates
// - Role changes
