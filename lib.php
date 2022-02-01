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
 */
function matrix_supports($feature): ?bool
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
        FEATURE_MOD_INTRO => false,
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
    $data = $form->get_data();

    $container = Container::instance();

    $moodleCourseRepository = $container->moodleCourseRepository();

    $courseId = Moodle\Domain\CourseId::fromString($moduleinfo->course);

    $course = $moodleCourseRepository->find($courseId);

    if (!$course instanceof Moodle\Domain\Course) {
        throw new \RuntimeException(\sprintf(
            'Could not find course with id %d.',
            $courseId->toInt(),
        ));
    }

    $target = Moodle\Domain\ModuleTarget::matrixTo();

    $config = $container->moodleConfiguration();

    if (
        $config->elementUrl()->toString() !== ''
        && \property_exists($data, 'target')
        && \is_string($data->target)
    ) {
        $target = Moodle\Domain\ModuleTarget::fromString($data->target);
    }

    $module = $container->moodleModuleService()->create(
        Moodle\Domain\ModuleName::fromString($data->name),
        Moodle\Domain\ModuleTopic::fromString($data->topic),
        $target,
        $courseId,
        Moodle\Domain\SectionId::fromInt($moduleinfo->section),
    );

    $matrixRoomService = $container->matrixRoomService();

    $moodleUserRepository = $container->moodleUserRepository();

    $staff = $moodleUserRepository->findAllStaffInCourseWithMatrixUserId($course->id());

    $userIdsOfStaff = Matrix\Domain\UserIdCollection::fromUserIds(...\array_map(static function (Moodle\Domain\User $user): Matrix\Domain\UserId {
        return $user->matrixUserId();
    }, $staff));

    $moodleRoomRepository = $container->moodleRoomRepository();
    $moodleRoomService = $container->moodleRoomService();

    // Now try to iterate over all the courses and groups and see if any of
    // the rooms need to be created
    $groups = groups_get_all_groups(
        $courseId->toInt(),
        0,
        0,
        'g.*',
        true,
    );

    if (\count($groups) > 0) {
        $moodleGroupRepository = $container->moodleGroupRepository();

        foreach ($groups as $g) {
            $groupId = Moodle\Domain\GroupId::fromString($g->id);

            $group = $moodleGroupRepository->find($groupId);

            if (!$group instanceof Moodle\Domain\Group) {
                throw new \RuntimeException(\sprintf(
                    'Could not find group with id %d.',
                    $groupId->toInt(),
                ));
            }

            $room = $moodleRoomRepository->findOneBy([
                'module_id' => $module->id()->toInt(),
                'group_id' => $group->id()->toInt(),
            ]);

            if (!$room instanceof Moodle\Domain\Room) {
                $room = $moodleRoomService->createRoomForCourseAndGroup(
                    $course,
                    $group,
                    $module,
                );
            }

            $users = $moodleUserRepository->findAllUsersEnrolledInCourseAndGroupWithMatrixUserId(
                $course->id(),
                $group->id(),
            );

            $matrixRoomService->synchronizeRoomMembers(
                $room->matrixRoomId(),
                Matrix\Domain\UserIdCollection::fromUserIds(...\array_map(static function (Moodle\Domain\User $user): Matrix\Domain\UserId {
                    return $user->matrixUserId();
                }, $users)),
                $userIdsOfStaff,
            );
        }

        return $module->id()->toInt();
    }

    $room = $moodleRoomRepository->findOneBy([
        'module_id' => $module->id()->toInt(),
        'group_id' => null,
    ]);

    if (!$room instanceof Moodle\Domain\Room) {
        $room = $moodleRoomService->createRoomForCourse(
            $course,
            $module,
        );
    }

    $users = $moodleUserRepository->findAllUsersEnrolledInCourseAndGroupWithMatrixUserId(
        $course->id(),
        Moodle\Domain\GroupId::fromInt(0),
    );

    $matrixRoomService->synchronizeRoomMembers(
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

    $moodleRoomRepository = $container->moodleRoomRepository();

    $rooms = $moodleRoomRepository->findAllBy([
        'module_id' => $module->id()->toInt(),
    ]);

    $matrixRoomService = $container->matrixRoomService();

    foreach ($rooms as $room) {
        $matrixRoomService->removeRoom($room->matrixRoomId());

        $moodleRoomRepository->remove($room);
    }

    $moodleModuleRepository->remove($module);

    return true;
}

/**
 * @see https://github.com/moodle/moodle/blob/v3.9.5/course/lib.php#L460-L542
 */
function matrix_get_coursemodule_info(object $moduleinfo): cached_cm_info
{
    $moduleId = Moodle\Domain\ModuleId::fromString($moduleinfo->instance);

    $module = Container::instance()->moodleModuleRepository()->findOneBy([
        'id' => $moduleId->toInt(),
    ]);

    if (!$module instanceof Moodle\Domain\Module) {
        throw new \RuntimeException(\sprintf(
            'Could not find module with id %d.',
            $moduleId->toInt(),
        ));
    }

    $onClickUrl = new moodle_url('/mod/matrix/view.php', [
        'id' => $moduleinfo->id,
    ]);

    $info = new cached_cm_info();

    $info->content = $module->topic()->toString();
    $info->onclick = \sprintf(
        "window.open('%s'); return false;",
        $onClickUrl->out(false),
    );

    return $info;
}

/**
 * @see https://docs.moodle.org/dev/Activity_modules#lib.php
 * @see https://github.com/moodle/moodle/blob/v3.9.5/course/modlib.php#L611-L614
 */
function matrix_update_instance(
    object $moduleinfo,
    mod_matrix_mod_form $form
): bool {
    global $DB;

    $moduleinfo->id = $moduleinfo->instance;

    $DB->update_record(
        Moodle\Infrastructure\DatabaseBasedModuleRepository::TABLE,
        $moduleinfo,
    );

    $container = Container::instance();

    $moduleId = Moodle\Domain\ModuleId::fromString($moduleinfo->instance);

    $module = $container->moodleModuleRepository()->findOneBy([
        'id' => $moduleId->toInt(),
    ]);

    if (!$module instanceof Moodle\Domain\Module) {
        throw new \RuntimeException(\sprintf(
            'Could not find module with id %d.',
            $moduleId->toInt(),
        ));
    }

    $course = $container->moodleCourseRepository()->find($module->courseId());

    if (!$course instanceof Moodle\Domain\Course) {
        throw new \RuntimeException(\sprintf(
            'Could not find course with id %d.',
            $module->courseId()->toInt(),
        ));
    }

    $rooms = $container->moodleRoomRepository()->findAllBy([
        'module_id' => $module->id()->toInt(),
    ]);

    if ([] === $rooms) {
        throw new \RuntimeException(\sprintf(
            'Could not find any rooms for module with id %d.',
            $module->id()->toInt(),
        ));
    }

    $moodleNameService = $container->moodleNameService();
    $moodleGroupRepository = $container->moodleGroupRepository();
    $matrixRoomService = $container->matrixRoomService();

    $name = $moodleNameService->forCourseAndModule(
        $course->shortName(),
        $module->name(),
    );

    foreach ($rooms as $room) {
        $groupId = $room->groupId();

        if ($groupId instanceof Moodle\Domain\GroupId) {
            $group = $moodleGroupRepository->find($groupId);

            if (!$group instanceof Moodle\Domain\Group) {
                throw new \RuntimeException(\sprintf(
                    'Could not find group with id %d.',
                    $groupId->toInt(),
                ));
            }

            $name = $moodleNameService->forGroupCourseAndModule(
                $group->name(),
                $course->shortName(),
                $module->name(),
            );
        }

        $matrixRoomService->updateRoom(
            $room->matrixRoomId(),
            $name,
            Matrix\Domain\RoomTopic::fromString($module->topic()->toString()),
        );
    }

    return true;
}

// TODO: Events API
// - Group edits
// - Course enrollment edits
// - Custom field (profile) updates
// - Role changes
