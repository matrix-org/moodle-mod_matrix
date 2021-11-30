<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Domain;

/**
 * @psalm-immutable
 */
final class Course
{
    private $id;
    private $fullName;

    private function __construct(
        CourseId $id,
        CourseFullName $fullName
    ) {
        $this->id = $id;
        $this->fullName = $fullName;
    }

    public static function create(
        CourseId $id,
        CourseFullName $fullName
    ): self {
        return new self(
            $id,
            $fullName,
        );
    }

    public function id(): CourseId
    {
        return $this->id;
    }

    public function fullName(): CourseFullName
    {
        return $this->fullName;
    }
}
