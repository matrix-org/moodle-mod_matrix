<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Matrix\Domain;

final class Module
{
    private $id;
    private $type;
    private $name;
    private $courseId;
    private $timecreated;
    private $timemodified;

    private function __construct(
        ModuleId $moduleId,
        Type $type,
        Name $name,
        CourseId $courseId,
        Timestamp $timecreated,
        Timestamp $timemodified
    ) {
        $this->id = $moduleId;
        $this->type = $type;
        $this->name = $name;
        $this->courseId = $courseId;
        $this->timecreated = $timecreated;
        $this->timemodified = $timemodified;
    }

    public static function create(
        ModuleId $moduleId,
        Type $type,
        Name $name,
        CourseId $courseId,
        Timestamp $timecreated,
        Timestamp $timemodified
    ): self {
        return new self(
            $moduleId,
            $type,
            $name,
            $courseId,
            $timecreated,
            $timemodified
        );
    }

    public function id(): ModuleId
    {
        return $this->id;
    }

    public function type(): Type
    {
        return $this->type;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function courseId(): CourseId
    {
        return $this->courseId;
    }

    public function timecreated(): Timestamp
    {
        return $this->timecreated;
    }

    public function timemodified(): Timestamp
    {
        return $this->timemodified;
    }
}
