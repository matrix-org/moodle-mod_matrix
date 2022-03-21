<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Plugin\Infrastructure;

use mod_matrix\Matrix;
use mod_matrix\Moodle;
use mod_matrix\Plugin;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Plugin\Infrastructure\DatabaseBasedRoomRepository
 *
 * @uses \mod_matrix\Matrix\Domain\RoomId
 * @uses \mod_matrix\Moodle\Domain\CourseId
 * @uses \mod_matrix\Moodle\Domain\GroupId
 * @uses \mod_matrix\Moodle\Domain\SectionId
 * @uses \mod_matrix\Moodle\Domain\Timestamp
 * @uses \mod_matrix\Plugin\Domain\ModuleId
 * @uses \mod_matrix\Plugin\Domain\Room
 * @uses \mod_matrix\Plugin\Domain\RoomId
 * @uses \mod_matrix\Plugin\Infrastructure\RoomNormalizer
 */
final class DatabaseBasedRoomRepositoryTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testConstants(): void
    {
        self::assertSame('matrix_rooms', Plugin\Infrastructure\DatabaseBasedRoomRepository::TABLE);
    }

    public function testFindOneByReturnsNullWhenRoomCouldNotBeFound(): void
    {
        $faker = self::faker()->unique();

        $conditions = [
            'group_id' => $faker->numberBetween(1),
            'module_id' => $faker->numberBetween(1),
        ];

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('get_record')
            ->with(
                self::identicalTo(Plugin\Infrastructure\DatabaseBasedRoomRepository::TABLE),
                self::identicalTo($conditions),
                self::identicalTo('*'),
                self::identicalTo(IGNORE_MISSING),
            )
            ->willReturn(null);

        $roomRepository = new Plugin\Infrastructure\DatabaseBasedRoomRepository(
            $database,
            new Plugin\Infrastructure\RoomNormalizer(),
        );

        self::assertNull($roomRepository->findOneBy($conditions));
    }

    public function testFindOneByReturnsRoomWhenRoomCouldBeFound(): void
    {
        $faker = self::faker();

        $moduleId = $faker->numberBetween(1);
        $groupId = $faker->numberBetween(1);

        $conditions = [
            'group_id' => $groupId,
            'module_id' => $moduleId,
        ];

        $normalized = (object) [
            'group_id' => $groupId,
            'id' => $faker->numberBetween(1),
            'module_id' => $moduleId,
            'room_id' => $faker->sha1(),
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
        ];

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('get_record')
            ->with(
                self::identicalTo(Plugin\Infrastructure\DatabaseBasedRoomRepository::TABLE),
                self::identicalTo($conditions),
                self::identicalTo('*'),
                self::identicalTo(IGNORE_MISSING),
            )
            ->willReturn($normalized);

        $roomRepository = new Plugin\Infrastructure\DatabaseBasedRoomRepository(
            $database,
            new Plugin\Infrastructure\RoomNormalizer(),
        );

        $expected = Plugin\Domain\Room::create(
            Plugin\Domain\RoomId::fromString((string) $normalized->id),
            Plugin\Domain\ModuleId::fromString((string) $normalized->module_id),
            Moodle\Domain\GroupId::fromString((string) $normalized->group_id),
            Matrix\Domain\RoomId::fromString($normalized->room_id),
            Moodle\Domain\Timestamp::fromString((string) $normalized->timecreated),
            Moodle\Domain\Timestamp::fromString((string) $normalized->timemodified),
        );

        self::assertEquals($expected, $roomRepository->findOneBy($conditions));
    }

    public function testFindAllByReturnsRoomsForConditions(): void
    {
        $faker = self::faker();

        $moduleId = $faker->numberBetween(1);

        $conditions = [
            'module_id' => $moduleId,
        ];

        $one = (object) [
            'group_id' => $faker->numberBetween(1),
            'id' => $faker->numberBetween(1),
            'module_id' => $moduleId,
            'room_id' => $faker->sha1(),
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
        ];

        $two = (object) [
            'group_id' => null,
            'id' => $faker->numberBetween(1),
            'module_id' => $moduleId,
            'room_id' => $faker->sha1(),
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
        ];

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('get_records')
            ->with(
                self::identicalTo(Plugin\Infrastructure\DatabaseBasedRoomRepository::TABLE),
                self::identicalTo($conditions),
            )
            ->willReturn([
                $one,
                $two,
            ]);

        $roomRepository = new Plugin\Infrastructure\DatabaseBasedRoomRepository(
            $database,
            new Plugin\Infrastructure\RoomNormalizer(),
        );

        $expected = [
            Plugin\Domain\Room::create(
                Plugin\Domain\RoomId::fromString((string) $one->id),
                Plugin\Domain\ModuleId::fromString((string) $one->module_id),
                Moodle\Domain\GroupId::fromString((string) $one->group_id),
                Matrix\Domain\RoomId::fromString($one->room_id),
                Moodle\Domain\Timestamp::fromString((string) $one->timecreated),
                Moodle\Domain\Timestamp::fromString((string) $one->timemodified),
            ),
            Plugin\Domain\Room::create(
                Plugin\Domain\RoomId::fromString((string) $two->id),
                Plugin\Domain\ModuleId::fromString((string) $two->module_id),
                null,
                Matrix\Domain\RoomId::fromString($two->room_id),
                Moodle\Domain\Timestamp::fromString((string) $two->timecreated),
                Moodle\Domain\Timestamp::fromString((string) $two->timemodified),
            ),
        ];

        self::assertEquals($expected, $roomRepository->findAllBy($conditions));
    }

    public function testFindAllReturnsRooms(): void
    {
        $faker = self::faker();

        $one = (object) [
            'group_id' => $faker->numberBetween(1),
            'id' => $faker->numberBetween(1),
            'module_id' => $faker->numberBetween(1),
            'room_id' => $faker->sha1(),
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
        ];

        $two = (object) [
            'group_id' => null,
            'id' => $faker->numberBetween(1),
            'module_id' => $faker->numberBetween(1),
            'room_id' => $faker->sha1(),
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
        ];

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('get_records')
            ->with(self::identicalTo(Plugin\Infrastructure\DatabaseBasedRoomRepository::TABLE))
            ->willReturn([
                $one,
                $two,
            ]);

        $roomRepository = new Plugin\Infrastructure\DatabaseBasedRoomRepository(
            $database,
            new Plugin\Infrastructure\RoomNormalizer(),
        );

        $expected = [
            Plugin\Domain\Room::create(
                Plugin\Domain\RoomId::fromString((string) $one->id),
                Plugin\Domain\ModuleId::fromString((string) $one->module_id),
                Moodle\Domain\GroupId::fromString((string) $one->group_id),
                Matrix\Domain\RoomId::fromString($one->room_id),
                Moodle\Domain\Timestamp::fromString((string) $one->timecreated),
                Moodle\Domain\Timestamp::fromString((string) $one->timemodified),
            ),
            Plugin\Domain\Room::create(
                Plugin\Domain\RoomId::fromString((string) $two->id),
                Plugin\Domain\ModuleId::fromString((string) $two->module_id),
                null,
                Matrix\Domain\RoomId::fromString($two->room_id),
                Moodle\Domain\Timestamp::fromString((string) $two->timecreated),
                Moodle\Domain\Timestamp::fromString((string) $two->timemodified),
            ),
        ];

        self::assertEquals($expected, $roomRepository->findAll());
    }

    public function testSaveInsertsRecordForRoomWhenRoomDoesNotHaveId(): void
    {
        $faker = self::faker();

        $id = $faker->numberBetween(1);

        $groupId = Moodle\Domain\GroupId::fromInt($faker->numberBetween(1));

        $room = Plugin\Domain\Room::create(
            Plugin\Domain\RoomId::unknown(),
            Plugin\Domain\ModuleId::fromInt($faker->numberBetween(1)),
            $groupId,
            Matrix\Domain\RoomId::fromString($faker->sha1()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
        );

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('insert_record')
            ->with(
                self::identicalTo(Plugin\Infrastructure\DatabaseBasedRoomRepository::TABLE),
                self::equalTo((object) [
                    'group_id' => $groupId->toInt(),
                    'id' => $room->id()->toInt(),
                    'module_id' => $room->moduleId()->toInt(),
                    'room_id' => $room->matrixRoomId()->toString(),
                    'timecreated' => $room->timecreated()->toInt(),
                    'timemodified' => $room->timemodified()->toInt(),
                ]),
            )
            ->willReturn($id);

        $roomRepository = new Plugin\Infrastructure\DatabaseBasedRoomRepository(
            $database,
            new Plugin\Infrastructure\RoomNormalizer(),
        );

        $roomRepository->save($room);

        self::assertEquals($id, $room->id()->toInt());
    }

    public function testSaveUpdatesRecordForRoomWhenRoomHasId(): void
    {
        $faker = self::faker();

        $id = Plugin\Domain\RoomId::fromInt($faker->numberBetween(1));
        $groupId = Moodle\Domain\GroupId::fromInt($faker->numberBetween(1));

        $room = Plugin\Domain\Room::create(
            $id,
            Plugin\Domain\ModuleId::fromInt($faker->numberBetween(1)),
            $groupId,
            Matrix\Domain\RoomId::fromString($faker->sha1()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
        );

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('update_record')
            ->with(
                self::identicalTo(Plugin\Infrastructure\DatabaseBasedRoomRepository::TABLE),
                self::equalTo((object) [
                    'group_id' => $groupId->toInt(),
                    'id' => $room->id()->toInt(),
                    'module_id' => $room->moduleId()->toInt(),
                    'room_id' => $room->matrixRoomId()->toString(),
                    'timecreated' => $room->timecreated()->toInt(),
                    'timemodified' => $room->timemodified()->toInt(),
                ]),
            );

        $roomRepository = new Plugin\Infrastructure\DatabaseBasedRoomRepository(
            $database,
            new Plugin\Infrastructure\RoomNormalizer(),
        );

        $roomRepository->save($room);

        self::assertSame($id, $room->id());
    }

    public function testRemoveRejectsRoomWhenRoomHasNotYetBeenPersisted(): void
    {
        $faker = self::faker();

        $room = Plugin\Domain\Room::create(
            Plugin\Domain\RoomId::unknown(),
            Plugin\Domain\ModuleId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\GroupId::fromInt($faker->numberBetween(1)),
            Matrix\Domain\RoomId::fromString($faker->sha1()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
        );

        $database = $this->createMock(\moodle_database::class);

        $roomRepository = new Plugin\Infrastructure\DatabaseBasedRoomRepository(
            $database,
            new Plugin\Infrastructure\RoomNormalizer(),
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Can not remove room that has not yet been persisted.');

        $roomRepository->remove($room);
    }

    public function testRemoveDeletesRecordForRoomWhenRoomHasBeenPersisted(): void
    {
        $faker = self::faker();

        $room = Plugin\Domain\Room::create(
            Plugin\Domain\RoomId::fromInt($faker->numberBetween(1)),
            Plugin\Domain\ModuleId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\GroupId::fromInt($faker->numberBetween(1)),
            Matrix\Domain\RoomId::fromString($faker->sha1()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
        );

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('delete_records')
            ->with(
                self::identicalTo(Plugin\Infrastructure\DatabaseBasedRoomRepository::TABLE),
                self::identicalTo([
                    'id' => $room->id()->toInt(),
                ]),
            );

        $roomRepository = new Plugin\Infrastructure\DatabaseBasedRoomRepository(
            $database,
            new Plugin\Infrastructure\RoomNormalizer(),
        );

        $roomRepository->remove($room);
    }
}
