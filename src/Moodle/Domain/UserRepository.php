<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Domain;

interface UserRepository
{
    /**
     * @return array<int, User>
     */
    public function findAllStaffInCourseWithMatrixUserId(CourseId $courseId): array;

    /**
     * @return array<int, User>
     */
    public function findAllUsersEnrolledInCourseAndGroupWithMatrixUserId(
        CourseId $courseId,
        GroupId $groupId
    ): array;
}
