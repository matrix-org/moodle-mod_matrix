<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Infrastructure;

use context_course;
use mod_matrix\Matrix;
use mod_matrix\Moodle;

final class MoodleFunctionBasedUserRepository implements Moodle\Domain\UserRepository
{
    public function findAllStaffInCourse(Moodle\Domain\CourseId $courseId): array
    {
        $context = context_course::instance($courseId->toInt());

        $users = get_users_by_capability(
            $context,
            'mod/matrix:staff',
        );

        if (!\is_array($users)) {
            return [];
        }

        return \array_map(static function (object $user): Moodle\Domain\User {
            $matrixUserId = self::matrixUserIdOf($user);

            return Moodle\Domain\User::create($matrixUserId);
        }, \array_values($users));
    }

    public function findAllUsersEnrolledInCourseAndGroup(
        Moodle\Domain\CourseId $courseId,
        Moodle\Domain\GroupId $groupId
    ): array {
        $context = context_course::instance($courseId->toInt());

        $users = get_enrolled_users(
            $context,
            'mod/matrix:view',
            $groupId->toInt(),
        );

        if (!\is_array($users)) {
            return [];
        }

        return \array_map(static function (object $user): Moodle\Domain\User {
            $matrixUserId = self::matrixUserIdOf($user);

            return Moodle\Domain\User::create($matrixUserId);
        }, \array_values($users));
    }

    private static function matrixUserIdOf(object $user): ?Matrix\Domain\UserId
    {
        profile_load_custom_fields($user);

        if (!\property_exists($user, 'profile')) {
            return null;
        }

        if (!\is_array($user->profile)) {
            return null;
        }

        if (!\array_key_exists('matrix_user_id', $user->profile)) {
            return null;
        }

        $matrixUserId = $user->profile['matrix_user_id'];

        if (!\is_string($matrixUserId)) {
            return null;
        }

        if ('' === \trim($matrixUserId)) {
            return null;
        }

        return Matrix\Domain\UserId::fromString($matrixUserId);
    }
}
