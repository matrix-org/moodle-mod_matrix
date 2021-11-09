<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Domain;

use mod_matrix\Matrix;

final class Room
{
    private $id;
    private $moduleId;
    private $groupId;
    private $matrixRoomId;
    private $timecreated;
    private $timemodified;

    private function __construct(
        RoomId $id,
        ModuleId $moduleId,
        ?GroupId $groupId,
        Matrix\Domain\RoomId $matrixRoomId,
        Timestamp $timecreated,
        Timestamp $timemodified
    ) {
        $this->id = $id;
        $this->moduleId = $moduleId;
        $this->groupId = $groupId;
        $this->matrixRoomId = $matrixRoomId;
        $this->timecreated = $timecreated;
        $this->timemodified = $timemodified;
    }

    public static function create(
        RoomId $id,
        ModuleId $moduleId,
        ?GroupId $groupId,
        Matrix\Domain\RoomId $matrixRoomId,
        Timestamp $timecreated,
        Timestamp $timemodified
    ): self {
        return new self(
            $id,
            $moduleId,
            $groupId,
            $matrixRoomId,
            $timecreated,
            $timemodified,
        );
    }

    public function id(): RoomId
    {
        return $this->id;
    }

    public function moduleId(): ModuleId
    {
        return $this->moduleId;
    }

    public function groupId(): ?GroupId
    {
        return $this->groupId;
    }

    public function matrixRoomId(): Matrix\Domain\RoomId
    {
        return $this->matrixRoomId;
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
