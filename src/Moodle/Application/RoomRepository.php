<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Application;

use mod_matrix\Moodle;

interface RoomRepository
{
    public function findOneBy(array $conditions): ?Moodle\Domain\Room;

    /**
     * @return array<int, Moodle\Domain\Room>
     */
    public function findAll(): array;

    /**
     * @return array<int, Moodle\Domain\Room>
     */
    public function findAllBy(array $conditions): array;

    public function save(Moodle\Domain\Room $room): void;

    /**
     * @throws \InvalidArgumentException
     */
    public function remove(Moodle\Domain\Room $room): void;
}
