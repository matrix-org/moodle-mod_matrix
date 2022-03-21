<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Plugin\Domain;

use mod_matrix\Matrix;
use mod_matrix\Moodle;
use mod_matrix\Plugin;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Plugin\Domain\Room
 *
 * @uses \mod_matrix\Matrix\Domain\RoomId
 * @uses \mod_matrix\Moodle\Domain\CourseId
 * @uses \mod_matrix\Moodle\Domain\GroupId
 * @uses \mod_matrix\Moodle\Domain\Timestamp
 * @uses \mod_matrix\Plugin\Domain\ModuleId
 * @uses \mod_matrix\Plugin\Domain\ModuleName
 * @uses \mod_matrix\Plugin\Domain\ModuleType
 * @uses \mod_matrix\Plugin\Domain\RoomId
 */
final class RoomTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsRoomWhenGroupIdIsNull(): void
    {
        $faker = self::faker();

        $roomId = Plugin\Domain\RoomId::fromInt($faker->numberBetween(1));
        $moduleId = Plugin\Domain\ModuleId::fromInt($faker->numberBetween(1));
        $groupId = null;
        $matrixRoomId = Matrix\Domain\RoomId::fromString($faker->sha1());
        $timecreated = Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp());
        $timemodified = Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp());

        $room = Plugin\Domain\Room::create(
            $roomId,
            $moduleId,
            $groupId,
            $matrixRoomId,
            $timecreated,
            $timemodified,
        );

        self::assertSame($roomId, $room->id());
        self::assertSame($moduleId, $room->moduleId());
        self::assertSame($groupId, $room->groupId());
        self::assertSame($matrixRoomId, $room->matrixRoomId());
        self::assertSame($timemodified, $room->timemodified());
        self::assertSame($timecreated, $room->timecreated());
    }

    public function testCreateReturnsRoomWhenGroupIdIsNotNull(): void
    {
        $faker = self::faker();

        $roomId = Plugin\Domain\RoomId::fromInt($faker->numberBetween(1));
        $moduleId = Plugin\Domain\ModuleId::fromInt($faker->numberBetween(1));
        $groupId = Moodle\Domain\GroupId::fromInt($faker->numberBetween(1));
        $matrixRoomId = Matrix\Domain\RoomId::fromString($faker->sha1());
        $timecreated = Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp());
        $timemodified = Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp());

        $room = Plugin\Domain\Room::create(
            $roomId,
            $moduleId,
            $groupId,
            $matrixRoomId,
            $timecreated,
            $timemodified,
        );

        self::assertSame($roomId, $room->id());
        self::assertSame($moduleId, $room->moduleId());
        self::assertSame($groupId, $room->groupId());
        self::assertSame($matrixRoomId, $room->matrixRoomId());
        self::assertSame($timemodified, $room->timemodified());
        self::assertSame($timecreated, $room->timecreated());
    }
}
