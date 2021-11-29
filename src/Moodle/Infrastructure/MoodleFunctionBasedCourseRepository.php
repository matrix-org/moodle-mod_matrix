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
    public function find(Moodle\Domain\CourseId $courseId): ?Moodle\Domain\Course
    {
        $course = get_course($courseId->toInt());

        if (!\is_object($course)) {
            return null;
        }

        if (!isset($course->fullname)) {
            throw new \RuntimeException('Expected object to have a fullname property, but it does not.');
        }

        if (!\is_string($course->fullname)) {
            throw new \RuntimeException(\sprintf(
                'Expected fullname property to be a string, got %s instead.',
                \is_object($course->fullname) ? \get_class($course->fullname) : \gettype($course->fullname),
            ));
        }

        return Moodle\Domain\Course::create(
            $courseId,
            Moodle\Domain\Name::fromString($course->fullname),
        );
    }
}
