<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Matrix\Repository;

use Ergebnis\Test\Util;
use mod_matrix\Matrix;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Matrix\Repository\RoomRepository
 */
final class RoomRepositoryTest extends Framework\TestCase
{
    use Util\Helper;

    public function testFindOneByReturnsNullWhenRoomCouldNotBeFound(): void
    {
        $faker = self::faker()->unique();

        $conditions = [
            'course_id' => $faker->numberBetween(1),
            'group_id' => $faker->numberBetween(1),
        ];

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('get_record')
            ->with(
                self::identicalTo('matrix_rooms'),
                self::identicalTo($conditions),
                self::identicalTo('*'),
                self::identicalTo(IGNORE_MISSING)
            )
            ->willReturn(null);

        $roomRepository = new Matrix\Repository\RoomRepository($database);

        self::assertNull($roomRepository->findOneBy($conditions));
    }

    public function testFindOneByReturnsRoomWhenRoomCouldBeFound(): void
    {
        $faker = self::faker();

        $courseId = $faker->numberBetween(1);
        $groupId = $faker->numberBetween(1);

        $conditions = [
            'course_id' => $courseId,
            'group_id' => $groupId,
        ];

        $room = (object) [
            'course_id' => $courseId,
            'group_id' => $groupId,
            'id' => $faker->numberBetween(1),
            'room_id' => $faker->sha1(),
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
        ];

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('get_record')
            ->with(
                self::identicalTo('matrix_rooms'),
                self::identicalTo($conditions),
                self::identicalTo('*'),
                self::identicalTo(IGNORE_MISSING)
            )
            ->willReturn($room);

        $roomRepository = new Matrix\Repository\RoomRepository($database);

        self::assertSame($room, $roomRepository->findOneBy($conditions));
    }

    public function testFindAllByReturnsRoomsForConditions(): void
    {
        $faker = self::faker();

        $courseId = $faker->numberBetween(1);
        $groupId = $faker->numberBetween(1);

        $conditions = [
            'course_id' => $courseId,
            'group_id' => $groupId,
        ];

        $rooms = [
            (object) [
                'course_id' => $courseId,
                'group_id' => $groupId,
                'id' => $faker->numberBetween(1),
                'room_id' => $faker->sha1(),
                'timecreated' => $faker->dateTime()->getTimestamp(),
                'timemodified' => $faker->dateTime()->getTimestamp(),
            ],
            (object) [
                'course_id' => $courseId,
                'group_id' => $groupId,
                'id' => $faker->numberBetween(1),
                'room_id' => $faker->sha1(),
                'timecreated' => $faker->dateTime()->getTimestamp(),
                'timemodified' => $faker->dateTime()->getTimestamp(),
            ],
        ];

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('get_records')
            ->with(
                self::identicalTo('matrix_rooms'),
                self::identicalTo($conditions)
            )
            ->willReturn($rooms);

        $roomRepository = new Matrix\Repository\RoomRepository($database);

        self::assertSame($rooms, $roomRepository->findAllBy($conditions));
    }
}
