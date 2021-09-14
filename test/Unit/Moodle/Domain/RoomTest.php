<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Moodle\Domain;

use Ergebnis\Test\Util;
use mod_matrix\Matrix;
use mod_matrix\Moodle;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Moodle\Domain\Room
 *
 * @uses \mod_matrix\Matrix\Domain\RoomId
 * @uses \mod_matrix\Moodle\Domain\CourseId
 * @uses \mod_matrix\Moodle\Domain\GroupId
 * @uses \mod_matrix\Moodle\Domain\ModuleId
 * @uses \mod_matrix\Moodle\Domain\Name
 * @uses \mod_matrix\Moodle\Domain\RoomId
 * @uses \mod_matrix\Moodle\Domain\Timestamp
 * @uses \mod_matrix\Moodle\Domain\Type
 */
final class RoomTest extends Framework\TestCase
{
    use Util\Helper;

    public function testCreateReturnsRoomWhenGroupIdIsNull(): void
    {
        $faker = self::faker();

        $id = Moodle\Domain\RoomId::fromInt($faker->numberBetween(1));
        $moduleId = Moodle\Domain\ModuleId::fromInt($faker->numberBetween(1));
        $groupId = null;
        $matrixRoomId = Matrix\Domain\RoomId::fromString($faker->sha1());
        $timecreated = Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp());
        $timemodified = Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp());

        $room = Moodle\Domain\Room::create(
            $id,
            $moduleId,
            $groupId,
            $matrixRoomId,
            $timecreated,
            $timemodified
        );

        self::assertSame($id, $room->id());
        self::assertSame($moduleId, $room->moduleId());
        self::assertSame($groupId, $room->groupId());
        self::assertSame($matrixRoomId, $room->matrixRoomId());
        self::assertSame($timemodified, $room->timemodified());
        self::assertSame($timecreated, $room->timecreated());
    }

    public function testCreateReturnsRoomWhenGroupIdIsNotNull(): void
    {
        $faker = self::faker();

        $id = Moodle\Domain\RoomId::fromInt($faker->numberBetween(1));
        $moduleId = Moodle\Domain\ModuleId::fromInt($faker->numberBetween(1));
        $groupId = Moodle\Domain\GroupId::fromInt($faker->numberBetween(1));
        $matrixRoomId = Matrix\Domain\RoomId::fromString($faker->sha1());
        $timecreated = Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp());
        $timemodified = Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp());

        $room = Moodle\Domain\Room::create(
            $id,
            $moduleId,
            $groupId,
            $matrixRoomId,
            $timecreated,
            $timemodified
        );

        self::assertSame($id, $room->id());
        self::assertSame($moduleId, $room->moduleId());
        self::assertSame($groupId, $room->groupId());
        self::assertSame($matrixRoomId, $room->matrixRoomId());
        self::assertSame($timemodified, $room->timemodified());
        self::assertSame($timecreated, $room->timecreated());
    }
}
