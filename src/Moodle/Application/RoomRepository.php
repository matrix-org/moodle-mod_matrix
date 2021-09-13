<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Application;

interface RoomRepository
{
    public function findOneBy(array $conditions): ?object;

    /**
     * @return array<int, object>
     */
    public function findAllBy(array $conditions): array;

    public function save(object $room): void;

    /**
     * @throws \InvalidArgumentException
     */
    public function remove(object $room): void;
}
