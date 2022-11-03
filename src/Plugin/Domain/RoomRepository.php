<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Plugin\Domain;

use mod_matrix\Plugin;

interface RoomRepository
{
    public function findOneBy(array $conditions): ?Plugin\Domain\Room;

    /**
     * @return array<int, Plugin\Domain\Room>
     */
    public function findAll(): array;

    /**
     * @return array<int, Plugin\Domain\Room>
     */
    public function findAllBy(array $conditions): array;

    public function save(Plugin\Domain\Room $room): void;

    /**
     * @throws \InvalidArgumentException
     */
    public function remove(Plugin\Domain\Room $room): void;
}
