<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Plugin\Domain;

use mod_matrix\Matrix;
use mod_matrix\Moodle;
use mod_matrix\Plugin;

final class Room
{
    private $id;
    private $moduleId;
    private $groupId;
    private $matrixRoomId;
    private $timecreated;
    private $timemodified;

    private function __construct(
        Plugin\Domain\RoomId $id,
        Plugin\Domain\ModuleId $moduleId,
        ?Moodle\Domain\GroupId $groupId,
        Matrix\Domain\RoomId $matrixRoomId,
        Moodle\Domain\Timestamp $timecreated,
        Moodle\Domain\Timestamp $timemodified
    ) {
        $this->id = $id;
        $this->moduleId = $moduleId;
        $this->groupId = $groupId;
        $this->matrixRoomId = $matrixRoomId;
        $this->timecreated = $timecreated;
        $this->timemodified = $timemodified;
    }

    public static function create(
        Plugin\Domain\RoomId $id,
        Plugin\Domain\ModuleId $moduleId,
        ?Moodle\Domain\GroupId $groupId,
        Matrix\Domain\RoomId $matrixRoomId,
        Moodle\Domain\Timestamp $timecreated,
        Moodle\Domain\Timestamp $timemodified
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

    public function id(): Plugin\Domain\RoomId
    {
        return $this->id;
    }

    public function moduleId(): Plugin\Domain\ModuleId
    {
        return $this->moduleId;
    }

    public function groupId(): ?Moodle\Domain\GroupId
    {
        return $this->groupId;
    }

    public function matrixRoomId(): Matrix\Domain\RoomId
    {
        return $this->matrixRoomId;
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
