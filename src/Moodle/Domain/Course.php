<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Moodle\Domain;

/**
 * @psalm-immutable
 */
final class Course
{
    private $id;
    private $fullName;
    private $shortName;

    private function __construct(
        CourseId $id,
        CourseFullName $fullName,
        CourseShortName $shortName
    ) {
        $this->id = $id;
        $this->fullName = $fullName;
        $this->shortName = $shortName;
    }

    public static function create(
        CourseId $id,
        CourseFullName $fullName,
        CourseShortName $shortName
    ): self {
        return new self(
            $id,
            $fullName,
            $shortName,
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

    public function shortName(): CourseShortName
    {
        return $this->shortName;
    }
}
