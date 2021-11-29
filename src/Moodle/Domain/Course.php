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
    private $name;

    private function __construct(
        CourseId $id,
        Name $name
    ) {
        $this->id = $id;
        $this->name = $name;
    }

    public static function create(
        CourseId $id,
        Name $name
    ): self {
        return new self(
            $id,
            $name,
        );
    }

    public function id(): CourseId
    {
        return $this->id;
    }

    public function name(): Name
    {
        return $this->name;
    }
}
