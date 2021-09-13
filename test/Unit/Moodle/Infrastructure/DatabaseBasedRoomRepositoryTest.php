<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Moodle\Infrastructure;

use Ergebnis\Test\Util;
use mod_matrix\Matrix;
use mod_matrix\Moodle;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Moodle\Infrastructure\DatabaseBasedRoomRepository
 *
 * @uses \mod_matrix\Matrix\Domain\RoomId
 * @uses \mod_matrix\Moodle\Domain\CourseId
 * @uses \mod_matrix\Moodle\Domain\GroupId
 * @uses \mod_matrix\Moodle\Domain\Room
 * @uses \mod_matrix\Moodle\Domain\RoomId
 * @uses \mod_matrix\Moodle\Domain\Timestamp
 * @uses \mod_matrix\Moodle\Infrastructure\RoomNormalizer
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

        $roomRepository = new Moodle\Infrastructure\DatabaseBasedRoomRepository(
            $database,
            new Moodle\Infrastructure\RoomNormalizer()
        );

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

        $normalized = (object) [
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
            ->willReturn($normalized);

        $roomRepository = new Moodle\Infrastructure\DatabaseBasedRoomRepository(
            $database,
            new Moodle\Infrastructure\RoomNormalizer()
        );

        $expected = Moodle\Domain\Room::create(
            Moodle\Domain\RoomId::fromString((string) $normalized->id),
            Moodle\Domain\CourseId::fromString((string) $normalized->course_id),
            Moodle\Domain\GroupId::fromString((string) $normalized->group_id),
            Matrix\Domain\RoomId::fromString($normalized->room_id),
            Moodle\Domain\Timestamp::fromString((string) $normalized->timecreated),
            Moodle\Domain\Timestamp::fromString((string) $normalized->timemodified)
        );

        self::assertEquals($expected, $roomRepository->findOneBy($conditions));
    }

    public function testFindAllByReturnsRoomsForConditions(): void
    {
        $faker = self::faker();

        $courseId = $faker->numberBetween(1);

        $conditions = [
            'course_id' => $courseId,
        ];

        $one = (object) [
            'course_id' => $courseId,
            'group_id' => $faker->numberBetween(1),
            'id' => $faker->numberBetween(1),
            'room_id' => $faker->sha1(),
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
        ];

        $two = (object) [
            'course_id' => $courseId,
            'group_id' => null,
            'id' => $faker->numberBetween(1),
            'room_id' => $faker->sha1(),
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
        ];

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('get_records')
            ->with(
                self::identicalTo('matrix_rooms'),
                self::identicalTo($conditions)
            )
            ->willReturn([
                $one,
                $two,
            ]);

        $roomRepository = new Moodle\Infrastructure\DatabaseBasedRoomRepository(
            $database,
            new Moodle\Infrastructure\RoomNormalizer()
        );

        $expected = [
            Moodle\Domain\Room::create(
                Moodle\Domain\RoomId::fromString((string) $one->id),
                Moodle\Domain\CourseId::fromString((string) $one->course_id),
                Moodle\Domain\GroupId::fromString((string) $one->group_id),
                Matrix\Domain\RoomId::fromString($one->room_id),
                Moodle\Domain\Timestamp::fromString((string) $one->timecreated),
                Moodle\Domain\Timestamp::fromString((string) $one->timemodified)
            ),
            Moodle\Domain\Room::create(
                Moodle\Domain\RoomId::fromString((string) $two->id),
                Moodle\Domain\CourseId::fromString((string) $two->course_id),
                null,
                Matrix\Domain\RoomId::fromString($two->room_id),
                Moodle\Domain\Timestamp::fromString((string) $two->timecreated),
                Moodle\Domain\Timestamp::fromString((string) $two->timemodified)
            ),
        ];

        self::assertEquals($expected, $roomRepository->findAllBy($conditions));
    }

    public function testFindAllReturnsRooms(): void
    {
        $faker = self::faker();

        $one = (object) [
            'course_id' => $faker->numberBetween(1),
            'group_id' => $faker->numberBetween(1),
            'id' => $faker->numberBetween(1),
            'room_id' => $faker->sha1(),
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
        ];

        $two = (object) [
            'course_id' => $faker->numberBetween(1),
            'group_id' => null,
            'id' => $faker->numberBetween(1),
            'room_id' => $faker->sha1(),
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
        ];

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('get_records')
            ->with(self::identicalTo('matrix_rooms'))
            ->willReturn([
                $one,
                $two,
            ]);

        $roomRepository = new Moodle\Infrastructure\DatabaseBasedRoomRepository(
            $database,
            new Moodle\Infrastructure\RoomNormalizer()
        );

        $expected = [
            Moodle\Domain\Room::create(
                Moodle\Domain\RoomId::fromString((string) $one->id),
                Moodle\Domain\CourseId::fromString((string) $one->course_id),
                Moodle\Domain\GroupId::fromString((string) $one->group_id),
                Matrix\Domain\RoomId::fromString($one->room_id),
                Moodle\Domain\Timestamp::fromString((string) $one->timecreated),
                Moodle\Domain\Timestamp::fromString((string) $one->timemodified)
            ),
            Moodle\Domain\Room::create(
                Moodle\Domain\RoomId::fromString((string) $two->id),
                Moodle\Domain\CourseId::fromString((string) $two->course_id),
                null,
                Matrix\Domain\RoomId::fromString($two->room_id),
                Moodle\Domain\Timestamp::fromString((string) $two->timecreated),
                Moodle\Domain\Timestamp::fromString((string) $two->timemodified)
            ),
        ];

        self::assertEquals($expected, $roomRepository->findAll());
    }

    public function testSaveInsertsRecordForRoomWhenRoomDoesNotHaveId(): void
    {
        $faker = self::faker();

        $id = $faker->numberBetween(1);

        $groupId = Moodle\Domain\GroupId::fromInt($faker->numberBetween(1));

        $room = Moodle\Domain\Room::create(
            Moodle\Domain\RoomId::unknown(),
            Moodle\Domain\CourseId::fromInt($faker->numberBetween(1)),
            $groupId,
            Matrix\Domain\RoomId::fromString($faker->sha1()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp())
        );

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('insert_record')
            ->with(
                self::identicalTo('matrix_rooms'),
                self::equalTo((object) [
                    'course_id' => $room->courseId()->toInt(),
                    'group_id' => $groupId->toInt(),
                    'id' => $room->id()->toInt(),
                    'room_id' => $room->matrixRoomId()->toString(),
                    'timecreated' => $room->timecreated()->toInt(),
                    'timemodified' => $room->timemodified()->toInt(),
                ])
            )
            ->willReturn($id);

        $roomRepository = new Moodle\Infrastructure\DatabaseBasedRoomRepository(
            $database,
            new Moodle\Infrastructure\RoomNormalizer()
        );

        $roomRepository->save($room);

        self::assertEquals($id, $room->id()->toInt());
    }

    public function testSaveUpdatesRecordForRoomWhenRoomHasId(): void
    {
        $faker = self::faker();

        $id = Moodle\Domain\RoomId::fromInt($faker->numberBetween(1));
        $groupId = Moodle\Domain\GroupId::fromInt($faker->numberBetween(1));

        $room = Moodle\Domain\Room::create(
            $id,
            Moodle\Domain\CourseId::fromInt($faker->numberBetween(1)),
            $groupId,
            Matrix\Domain\RoomId::fromString($faker->sha1()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp())
        );

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('update_record')
            ->with(
                self::identicalTo('matrix_rooms'),
                self::equalTo((object) [
                    'course_id' => $room->courseId()->toInt(),
                    'group_id' => $groupId->toInt(),
                    'id' => $room->id()->toInt(),
                    'room_id' => $room->matrixRoomId()->toString(),
                    'timecreated' => $room->timecreated()->toInt(),
                    'timemodified' => $room->timemodified()->toInt(),
                ])
            );

        $roomRepository = new Moodle\Infrastructure\DatabaseBasedRoomRepository(
            $database,
            new Moodle\Infrastructure\RoomNormalizer()
        );

        $roomRepository->save($room);

        self::assertSame($id, $room->id());
    }

    public function testRemoveRejectsRoomWhenRoomHasNotYetBeenPersisted(): void
    {
        $faker = self::faker();

        $room = Moodle\Domain\Room::create(
            Moodle\Domain\RoomId::unknown(),
            Moodle\Domain\CourseId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\GroupId::fromInt($faker->numberBetween(1)),
            Matrix\Domain\RoomId::fromString($faker->sha1()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp())
        );

        $database = $this->createMock(\moodle_database::class);

        $roomRepository = new Moodle\Infrastructure\DatabaseBasedRoomRepository(
            $database,
            new Moodle\Infrastructure\RoomNormalizer()
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Can not remove room that has not yet been persisted.');

        $roomRepository->remove($room);
    }

    public function testRemoveDeletesRecordForRoomWhenRoomHasBeenPersisted(): void
    {
        $faker = self::faker();

        $room = Moodle\Domain\Room::create(
            Moodle\Domain\RoomId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\CourseId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\GroupId::fromInt($faker->numberBetween(1)),
            Matrix\Domain\RoomId::fromString($faker->sha1()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp())
        );

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('delete_records')
            ->with(
                self::identicalTo('matrix_rooms'),
                self::identicalTo([
                    'id' => $room->id()->toInt(),
                ])
            );

        $roomRepository = new Moodle\Infrastructure\DatabaseBasedRoomRepository(
            $database,
            new Moodle\Infrastructure\RoomNormalizer()
        );

        $roomRepository->remove($room);
    }
}
