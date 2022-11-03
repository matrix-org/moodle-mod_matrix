<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Plugin\Infrastructure;

use mod_matrix\Plugin;

final class DatabaseBasedRoomRepository implements Plugin\Domain\RoomRepository
{
    public const TABLE = 'matrix_rooms';
    private $database;
    private $roomNormalizer;

    public function __construct(
        \moodle_database $database,
        Plugin\Infrastructure\RoomNormalizer $roomNormalizer
    ) {
        $this->database = $database;
        $this->roomNormalizer = $roomNormalizer;
    }

    public function findOneBy(array $conditions): ?Plugin\Domain\Room
    {
        $room = $this->database->get_record(
            self::TABLE,
            $conditions,
            '*',
            IGNORE_MISSING,
        );

        if (!\is_object($room)) {
            return null;
        }

        return $this->roomNormalizer->denormalize($room);
    }

    public function findAll(): array
    {
        /** @var array<int, object> $rooms */
        $rooms = $this->database->get_records(self::TABLE);

        return \array_map(function (object $room): Plugin\Domain\Room {
            return $this->roomNormalizer->denormalize($room);
        }, $rooms);
    }

    public function findAllBy(array $conditions): array
    {
        /** @var array<int, object> $rooms */
        $rooms = $this->database->get_records(
            self::TABLE,
            $conditions,
        );

        return \array_map(function (object $room): Plugin\Domain\Room {
            return $this->roomNormalizer->denormalize($room);
        }, $rooms);
    }

    public function save(Plugin\Domain\Room $room): void
    {
        if ($room->id()->equals(Plugin\Domain\RoomId::unknown())) {
            $id = $this->database->insert_record(
                self::TABLE,
                $this->roomNormalizer->normalize($room),
            );

            $reflection = new \ReflectionClass(Plugin\Domain\Room::class);

            $property = $reflection->getProperty('id');

            $property->setAccessible(true);
            $property->setValue(
                $room,
                Plugin\Domain\RoomId::fromInt((int) $id),
            );

            return;
        }

        $this->database->update_record(
            self::TABLE,
            $this->roomNormalizer->normalize($room),
        );
    }

    public function remove(Plugin\Domain\Room $room): void
    {
        if ($room->id()->equals(Plugin\Domain\RoomId::unknown())) {
            throw new \InvalidArgumentException('Can not remove room that has not yet been persisted.');
        }

        $this->database->delete_records(
            self::TABLE,
            [
                'id' => $room->id()->toInt(),
            ],
        );
    }
}
