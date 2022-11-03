<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Plugin\Domain;

use mod_matrix\Moodle;

interface UserRepository
{
    /**
     * @return array<int, User>
     */
    public function findAllStaffInCourseWithMatrixUserId(Moodle\Domain\CourseId $courseId): array;

    /**
     * @return array<int, User>
     */
    public function findAllUsersEnrolledInCourseAndGroupWithMatrixUserId(
        Moodle\Domain\CourseId $courseId,
        Moodle\Domain\GroupId $groupId
    ): array;
}
