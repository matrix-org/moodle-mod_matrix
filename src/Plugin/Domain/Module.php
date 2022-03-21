<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Plugin\Domain;

use mod_matrix\Moodle;

final class Module
{
    private $id;
    private $type;
    private $name;
    private $topic;
    private $target;
    private $courseId;
    private $sectionId;
    private $timecreated;
    private $timemodified;

    private function __construct(
        ModuleId $id,
        ModuleType $type,
        ModuleName $name,
        ModuleTopic $topic,
        ModuleTarget $target,
        Moodle\Domain\CourseId $courseId,
        Moodle\Domain\SectionId $sectionId,
        Moodle\Domain\Timestamp $timecreated,
        Moodle\Domain\Timestamp $timemodified
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->name = $name;
        $this->topic = $topic;
        $this->target = $target;
        $this->courseId = $courseId;
        $this->sectionId = $sectionId;
        $this->timecreated = $timecreated;
        $this->timemodified = $timemodified;
    }

    public static function create(
        ModuleId $id,
        ModuleType $type,
        ModuleName $name,
        ModuleTopic $topic,
        ModuleTarget $target,
        Moodle\Domain\CourseId $courseId,
        Moodle\Domain\SectionId $sectionId,
        Moodle\Domain\Timestamp $timecreated,
        Moodle\Domain\Timestamp $timemodified
    ): self {
        return new self(
            $id,
            $type,
            $name,
            $topic,
            $target,
            $courseId,
            $sectionId,
            $timecreated,
            $timemodified,
        );
    }

    public function id(): ModuleId
    {
        return $this->id;
    }

    public function type(): ModuleType
    {
        return $this->type;
    }

    public function name(): ModuleName
    {
        return $this->name;
    }

    public function topic(): ModuleTopic
    {
        return $this->topic;
    }

    public function target(): ModuleTarget
    {
        return $this->target;
    }

    public function courseId(): Moodle\Domain\CourseId
    {
        return $this->courseId;
    }

    public function sectionId(): Moodle\Domain\SectionId
    {
        return $this->sectionId;
    }

    public function timecreated(): Moodle\Domain\Timestamp
    {
        return $this->timecreated;
    }

    public function timemodified(): Moodle\Domain\Timestamp
    {
        return $this->timemodified;
    }
}
