<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Infrastructure;

use mod_matrix\Moodle;

final class DatabaseBasedRoomRepository implements Moodle\Application\RoomRepository
{
    private const TABLE = 'matrix_rooms';
    private $database;
    private $roomNormalizer;

    public function __construct(
        \moodle_database $database,
        RoomNormalizer $roomNormalizer
    ) {
        $this->database = $database;
        $this->roomNormalizer = $roomNormalizer;
    }

    public function findOneBy(array $conditions): ?Moodle\Domain\Room
    {
        $room = $this->database->get_record(
            self::TABLE,
            $conditions,
            '*',
            IGNORE_MISSING
        );

        if (!is_object($room)) {
            return null;
        }

        return $this->roomNormalizer->denormalize($room);
    }

    public function findAllBy(array $conditions): array
    {
        /** @var array<int, object> $rooms */
        $rooms = $this->database->get_records(
            self::TABLE,
            $conditions
        );

        return array_map(function (object $room): Moodle\Domain\Room {
            return $this->roomNormalizer->denormalize($room);
        }, $rooms);
    }

    public function save(Moodle\Domain\Room $room): void
    {
        if ($room->id()->equals(Moodle\Domain\RoomId::unknown())) {
            $id = $this->database->insert_record(
                self::TABLE,
                $this->roomNormalizer->normalize($room)
            );

            $reflection = new \ReflectionClass(Moodle\Domain\Room::class);

            $property = $reflection->getProperty('id');

            $property->setAccessible(true);
            $property->setValue(
                $room,
                Moodle\Domain\RoomId::fromInt((int) $id)
            );

            return;
        }

        $this->database->update_record(
            self::TABLE,
            $this->roomNormalizer->normalize($room)
        );
    }

    public function remove(Moodle\Domain\Room $room): void
    {
        if ($room->id()->equals(Moodle\Domain\RoomId::unknown())) {
            throw new \InvalidArgumentException('Can not remove room that has not yet been persisted.');
        }

        $this->database->delete_records(
            self::TABLE,
            [
                'id' => $room->id()->toInt(),
            ]
        );
    }
}
