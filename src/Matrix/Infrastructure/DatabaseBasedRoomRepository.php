<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Matrix\Infrastructure;

use mod_matrix\Matrix;

final class DatabaseBasedRoomRepository implements Matrix\Application\RoomRepository
{
    private const TABLE = 'matrix_rooms';
    private $database;

    public function __construct(\moodle_database $database)
    {
        $this->database = $database;
    }

    public function findOneBy(array $conditions): ?object
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

        return $room;
    }

    public function findAllBy(array $conditions): array
    {
        return $this->database->get_records(
            self::TABLE,
            $conditions
        );
    }

    public function save(object $room): void
    {
        if (!property_exists($room, 'id')) {
            $id = $this->database->insert_record(
                self::TABLE,
                $room
            );

            $room->id = $id;

            return;
        }

        $this->database->update_record(
            self::TABLE,
            $room
        );
    }

    public function remove(object $room): void
    {
        if (!property_exists($room, 'id')) {
            throw new \InvalidArgumentException('Can not remove room without identifier.');
        }

        $this->database->delete_records(
            self::TABLE,
            [
                'id' => $room->id,
            ]
        );
    }
}
