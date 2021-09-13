<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Moodle\Infrastructure;

use Ergebnis\Test\Util;
use mod_matrix\Moodle;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Moodle\Infrastructure\DatabaseBasedRoomRepository
 */
final class DatabaseBasedRoomRepositoryTest extends Framework\TestCase
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

        $roomRepository = new Moodle\Infrastructure\DatabaseBasedRoomRepository($database);

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

        $roomRepository = new Moodle\Infrastructure\DatabaseBasedRoomRepository($database);

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

        $roomRepository = new Moodle\Infrastructure\DatabaseBasedRoomRepository($database);

        self::assertSame($rooms, $roomRepository->findAllBy($conditions));
    }

    public function testSaveInsertsRecordForRoomWhenRoomDoesNotHaveId(): void
    {
        $faker = self::faker();

        $id = $faker->numberBetween(1);

        $room = (object) [
            'course_id' => $faker->numberBetween(1),
            'group_id' => $faker->numberBetween(1),
            'room_id' => $faker->sha1(),
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
        ];

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('insert_record')
            ->with(
                self::identicalTo('matrix_rooms'),
                self::identicalTo($room)
            )
            ->willReturn($id);

        $roomRepository = new Moodle\Infrastructure\DatabaseBasedRoomRepository($database);

        $roomRepository->save($room);

        self::assertSame($id, $room->id);
    }

    public function testSaveUpdatesRecordForRoomWhenRoomHasId(): void
    {
        $faker = self::faker();

        $id = $faker->numberBetween(1);

        $room = (object) [
            'course_id' => $faker->numberBetween(1),
            'group_id' => $faker->numberBetween(1),
            'id' => $id,
            'room_id' => $faker->sha1(),
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
        ];

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('update_record')
            ->with(
                self::identicalTo('matrix_rooms'),
                self::identicalTo($room)
            );

        $roomRepository = new Moodle\Infrastructure\DatabaseBasedRoomRepository($database);

        $roomRepository->save($room);

        self::assertSame($id, $room->id);
    }

    public function testRemoveRejectsRoomWhenRoomDoesNotHaveId(): void
    {
        $faker = self::faker();

        $id = $faker->numberBetween(1);

        $room = (object) [
            'course_id' => $faker->numberBetween(1),
            'group_id' => $faker->numberBetween(1),
            'room_id' => $faker->sha1(),
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
        ];

        $database = $this->createMock(\moodle_database::class);

        $roomRepository = new Moodle\Infrastructure\DatabaseBasedRoomRepository($database);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Can not remove room without identifier.');

        $roomRepository->remove($room);
    }

    public function testRemoveDeletesRecordForRoomWhenRoomHasId(): void
    {
        $faker = self::faker();

        $id = $faker->numberBetween(1);

        $room = (object) [
            'course_id' => $faker->numberBetween(1),
            'group_id' => $faker->numberBetween(1),
            'id' => $id,
            'room_id' => $faker->sha1(),
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
        ];

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('delete_records')
            ->with(
                self::identicalTo('matrix_rooms'),
                self::identicalTo([
                    'id' => $id,
                ])
            );

        $roomRepository = new Moodle\Infrastructure\DatabaseBasedRoomRepository($database);

        $roomRepository->remove($room);
    }
}
