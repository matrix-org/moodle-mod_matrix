<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Infrastructure;

use mod_matrix\Moodle;

final class MoodleFunctionBasedCourseRepository implements Moodle\Domain\CourseRepository
{
    public function find(Moodle\Domain\CourseId $courseId): ?object
    {
        return get_course($courseId->toInt());
    }
}
