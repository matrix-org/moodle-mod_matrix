<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

use mod_matrix\Container;
use mod_matrix\Matrix;
use mod_matrix\Moodle;

\defined('MOODLE_INTERNAL') || exit;

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
    if (!\is_string($feature)) {
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

    if (!\array_key_exists($feature, $features)) {
        return null;
    }

    return $features[$feature];
}

/**
 * @see https://docs.moodle.org/dev/Activity_modules#lib.php
 * @see https://github.com/moodle/moodle/blob/v3.9.5/course/modlib.php#L126-L131
 *
 * @throws \RuntimeException
 */
function matrix_add_instance(
    object $moduleinfo,
    mod_matrix_mod_form $form
): int {
    global $CFG;

    $data = $form->get_data();

    $container = Container::instance();

    $courseRepository = $container->courseRepository();
    $groupRepository = $container->groupRepository();
    $roomRepository = $container->roomRepository();
    $userRepository = $container->userRepository();
    $clock = $container->clock();

    $courseId = Moodle\Domain\CourseId::fromString($moduleinfo->course);

    $course = $courseRepository->find($courseId);

    if (!$course instanceof Moodle\Domain\Course) {
        throw new \RuntimeException(\sprintf(
            'Could not find course with id %d.',
            $courseId->toInt(),
        ));
    }

    $moduleService = $container->moduleService();

    $module = $moduleService->create(
        Moodle\Domain\ModuleName::fromString($data->name),
        $courseId,
        Moodle\Domain\SectionId::fromInt($moduleinfo->section),
    );

    // Now try to iterate over all the courses and groups and see if any of
    // the rooms need to be created
    $groups = groups_get_all_groups(
        $courseId->toInt(),
        0,
        0,
        'g.*',
        true,
    );

    $matrixRoomService = $container->matrixRoomService();

    $topic = Matrix\Domain\RoomTopic::fromString(\sprintf(
        '%s/course/view.php?id=%d',
        $CFG->wwwroot,
        $courseId->toInt(),
    ));

    $staff = $userRepository->findAllStaffInCourseWithMatrixUserId($course->id());

    $userIdsOfStaff = Matrix\Domain\UserIdCollection::fromUserIds(...\array_map(static function (Moodle\Domain\User $user): Matrix\Domain\UserId {
        return $user->matrixUserId();
    }, $staff));

    if (\count($groups) > 0) {
        foreach ($groups as $g) {
            $groupId = Moodle\Domain\GroupId::fromString($g->id);

            $group = $groupRepository->find($groupId);

            if (!$group instanceof Moodle\Domain\Group) {
                throw new \RuntimeException(\sprintf(
                    'Could not find group with id %d.',
                    $groupId->toInt(),
                ));
            }

            $name = Matrix\Domain\RoomName::fromString(\sprintf(
                '%s: %s (%s)',
                $group->name()->toString(),
                $course->name()->toString(),
                $module->name()->toString(),
            ));

            $room = $roomRepository->findOneBy([
                'module_id' => $module->id()->toInt(),
                'group_id' => $group->id()->toInt(),
            ]);

            if (!$room instanceof Moodle\Domain\Room) {
                $matrixRoomId = $matrixRoomService->createRoom(
                    $name,
                    $topic,
                    [
                        'org.matrix.moodle.course_id' => $course->id()->toInt(),
                        'org.matrix.moodle.group_id' => $group->id()->toInt(),
                    ],
                );

                $room = Moodle\Domain\Room::create(
                    Moodle\Domain\RoomId::unknown(),
                    $module->id(),
                    $group->id(),
                    $matrixRoomId,
                    Moodle\Domain\Timestamp::fromInt($clock->now()->getTimestamp()),
                    Moodle\Domain\Timestamp::fromInt(0),
                );

                $roomRepository->save($room);
            }

            $users = $userRepository->findAllUsersEnrolledInCourseAndGroupWithMatrixUserId(
                $course->id(),
                $group->id(),
            );

            $matrixRoomService->synchronizeRoomMembersForRoom(
                $room->matrixRoomId(),
                Matrix\Domain\UserIdCollection::fromUserIds(...\array_map(static function (Moodle\Domain\User $user): Matrix\Domain\UserId {
                    return $user->matrixUserId();
                }, $users)),
                $userIdsOfStaff,
            );
        }

        return $module->id()->toInt();
    }

    $name = Matrix\Domain\RoomName::fromString(\sprintf(
        '%s (%s)',
        $course->name()->toString(),
        $module->name()->toString(),
    ));

    $room = $roomRepository->findOneBy([
        'module_id' => $module->id()->toInt(),
        'group_id' => null,
    ]);

    if (!$room instanceof Moodle\Domain\Room) {
        $matrixRoomId = $matrixRoomService->createRoom(
            $name,
            $topic,
            [
                'org.matrix.moodle.course_id' => $course->id()->toInt(),
            ],
        );

        $clock = $container->clock();

        $room = Moodle\Domain\Room::create(
            Moodle\Domain\RoomId::unknown(),
            $module->id(),
            null,
            $matrixRoomId,
            Moodle\Domain\Timestamp::fromInt($clock->now()->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt(0),
        );

        $roomRepository->save($room);
    }

    $users = $userRepository->findAllUsersEnrolledInCourseAndGroupWithMatrixUserId(
        $course->id(),
        Moodle\Domain\GroupId::fromInt(0),
    );

    $matrixRoomService->synchronizeRoomMembersForRoom(
        $room->matrixRoomId(),
        Matrix\Domain\UserIdCollection::fromUserIds(...\array_map(static function (Moodle\Domain\User $user): Matrix\Domain\UserId {
            return $user->matrixUserId();
        }, $users)),
        $userIdsOfStaff,
    );

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

    $moodleModuleRepository = $container->moodleModuleRepository();

    $module = $moodleModuleRepository->findOneBy([
        'id' => $id,
    ]);

    if (!$module instanceof Moodle\Domain\Module) {
        return false;
    }

    $roomRepository = $container->roomRepository();

    $rooms = $roomRepository->findAllBy([
        'module_id' => $module->id()->toInt(),
    ]);

    $matrixRoomService = $container->matrixRoomService();

    foreach ($rooms as $room) {
        $matrixRoomService->removeRoom($room->matrixRoomId());

        $roomRepository->remove($room);
    }

    $moodleModuleRepository->remove($module);

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
