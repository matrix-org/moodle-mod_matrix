<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Plugin\Infrastructure;

use context_course;
use mod_matrix\Matrix;
use mod_matrix\Moodle;
use mod_matrix\Plugin;

final class MoodleFunctionBasedUserRepository implements Plugin\Domain\UserRepository
{
    private $matrixUserIdLoader;

    public function __construct(Plugin\Domain\MatrixUserIdLoader $matrixUserIdLoader)
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

                $usersWithMatrixUserId[] = Plugin\Domain\User::create(
                    Plugin\Domain\UserId::fromString($user->id),
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

                $usersWithMatrixUserId[] = Plugin\Domain\User::create(
                    Plugin\Domain\UserId::fromString($user->id),
                    $matrixUserId,
                );

                return $usersWithMatrixUserId;
            },
            [],
        ));
    }
}
