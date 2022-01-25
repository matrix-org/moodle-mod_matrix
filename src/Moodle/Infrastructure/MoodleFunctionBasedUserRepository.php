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
    private $matrixUserIdLoader;

    public function __construct(Moodle\Domain\MatrixUserIdLoader $matrixUserIdLoader)
    {
        $this->matrixUserIdLoader = $matrixUserIdLoader;
    }

    public function findAllStaffInCourseWithMatrixUserId(Moodle\Domain\CourseId $courseId): array
    {
        $context = context_course::instance($courseId->toInt());

        $users = get_users_by_capability(
            $context,
            'mod/matrix:staff',
        );

        if (!\is_array($users)) {
            return [];
        }

        $matrixUserIdLoader = $this->matrixUserIdLoader;

        return \array_values(\array_reduce(
            $users,
            static function (array $usersWithMatrixUserId, object $user) use ($matrixUserIdLoader): array {
                $matrixUserId = $matrixUserIdLoader->load($user);

                if (!$matrixUserId instanceof Matrix\Domain\UserId) {
                    return $usersWithMatrixUserId;
                }

                $usersWithMatrixUserId[] = Moodle\Domain\User::create(
                    Moodle\Domain\UserId::fromString($user->id),
                    $matrixUserId,
                );

                return $usersWithMatrixUserId;
            },
            [],
        ));
    }

    public function findAllUsersEnrolledInCourseAndGroupWithMatrixUserId(
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

        $matrixUserIdLoader = $this->matrixUserIdLoader;

        return \array_values(\array_reduce(
            $users,
            static function (array $usersWithMatrixUserId, object $user) use ($matrixUserIdLoader): array {
                $matrixUserId = $matrixUserIdLoader->load($user);

                if (!$matrixUserId instanceof Matrix\Domain\UserId) {
                    return $usersWithMatrixUserId;
                }

                $usersWithMatrixUserId[] = Moodle\Domain\User::create(
                    Moodle\Domain\UserId::fromString($user->id),
                    $matrixUserId,
                );

                return $usersWithMatrixUserId;
            },
            [],
        ));
    }
}
