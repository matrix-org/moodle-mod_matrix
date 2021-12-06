<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Domain;

final class CourseNotFound extends \RuntimeException
{
    public static function for(CourseId $courseId): self
    {
        return new self(\sprintf(
            'Could not find course with id %d.',
            $courseId->toInt(),
        ));
    }
}
