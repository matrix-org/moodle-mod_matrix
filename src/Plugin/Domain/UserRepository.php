<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
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
