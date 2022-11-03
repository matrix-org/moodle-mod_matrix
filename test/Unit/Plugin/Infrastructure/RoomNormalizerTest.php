<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
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
 * @covers \mod_matrix\Plugin\Infrastructure\RoomNormalizer
 *
 * @uses \mod_matrix\Matrix\Domain\RoomId
 * @uses \mod_matrix\Moodle\Domain\CourseId
 * @uses \mod_matrix\Moodle\Domain\GroupId
 * @uses \mod_matrix\Moodle\Domain\Timestamp
 * @uses \mod_matrix\Plugin\Domain\Module
 * @uses \mod_matrix\Plugin\Domain\ModuleId
 * @uses \mod_matrix\Plugin\Domain\ModuleName
 * @uses \mod_matrix\Plugin\Domain\ModuleType
 * @uses \mod_matrix\Plugin\Domain\Room
 * @uses \mod_matrix\Plugin\Domain\RoomId
 */
final class RoomNormalizerTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testDenormalizeReturnsRoomFromNormalizedRoomWhenNumericFieldsAreIntegers(): void
    {
        $faker = self::faker();

        $groupId = $faker->numberBetween(1);
        $id = $faker->numberBetween(1);
        $matrixRoomId = $faker->sha1();
        $moduleId = $faker->numberBetween(1);
        $timecreated = $faker->dateTime()->getTimestamp();
        $timemodified = $faker->dateTime()->getTimestamp();

        $normalized = (object) [
            'group_id' => $groupId,
            'id' => $id,
            'module_id' => $moduleId,
            'room_id' => $matrixRoomId,
            'timecreated' => $timecreated,
            'timemodified' => $timemodified,
        ];

        $roomNormalizer = new Plugin\Infrastructure\RoomNormalizer();

        $denormalized = $roomNormalizer->denormalize($normalized);

        $expected = Plugin\Domain\Room::create(
            Plugin\Domain\RoomId::fromInt($id),
            Plugin\Domain\ModuleId::fromInt($moduleId),
            Moodle\Domain\GroupId::fromInt($groupId),
            Matrix\Domain\RoomId::fromString($matrixRoomId),
            Moodle\Domain\Timestamp::fromInt($timecreated),
            Moodle\Domain\Timestamp::fromInt($timemodified),
        );

        self::assertEquals($expected, $denormalized);
    }

    public function testDenormalizeReturnsRoomFromNormalizedRoomWhenNumericFieldsAreStrings(): void
    {
        $faker = self::faker();

        $groupId = (string) $faker->numberBetween(1);
        $id = (string) $faker->numberBetween(1);
        $matrixRoomId = $faker->sha1();
        $moduleId = (string) $faker->numberBetween(1);
        $timecreated = (string) $faker->dateTime()->getTimestamp();
        $timemodified = (string) $faker->dateTime()->getTimestamp();

        $normalized = (object) [
            'group_id' => $groupId,
            'id' => $id,
            'module_id' => $moduleId,
            'room_id' => $matrixRoomId,
            'timecreated' => $timecreated,
            'timemodified' => $timemodified,
        ];

        $roomNormalizer = new Plugin\Infrastructure\RoomNormalizer();

        $denormalized = $roomNormalizer->denormalize($normalized);

        $expected = Plugin\Domain\Room::create(
            Plugin\Domain\RoomId::fromString($id),
            Plugin\Domain\ModuleId::fromString($moduleId),
            Moodle\Domain\GroupId::fromString($groupId),
            Matrix\Domain\RoomId::fromString($matrixRoomId),
            Moodle\Domain\Timestamp::fromString($timecreated),
            Moodle\Domain\Timestamp::fromString($timemodified),
        );

        self::assertEquals($expected, $denormalized);
    }

    public function testDenormalizeReturnsRoomFromNormalizedRoomWhenGroupIdIsNull(): void
    {
        $faker = self::faker();

        $groupId = null;
        $id = $faker->numberBetween(1);
        $matrixRoomId = $faker->sha1();
        $moduleId = $faker->numberBetween(1);
        $timecreated = $faker->dateTime()->getTimestamp();
        $timemodified = $faker->dateTime()->getTimestamp();

        $normalized = (object) [
            'group_id' => $groupId,
            'id' => $id,
            'module_id' => $moduleId,
            'room_id' => $matrixRoomId,
            'timecreated' => $timecreated,
            'timemodified' => $timemodified,
        ];

        $roomNormalizer = new Plugin\Infrastructure\RoomNormalizer();

        $denormalized = $roomNormalizer->denormalize($normalized);

        $expected = Plugin\Domain\Room::create(
            Plugin\Domain\RoomId::fromInt($id),
            Plugin\Domain\ModuleId::fromInt($moduleId),
            null,
            Matrix\Domain\RoomId::fromString($matrixRoomId),
            Moodle\Domain\Timestamp::fromInt($timecreated),
            Moodle\Domain\Timestamp::fromInt($timemodified),
        );

        self::assertEquals($expected, $denormalized);
    }

    public function testNormalizeReturnsRoomFromDenormalizedRoom(): void
    {
        $faker = self::faker();

        $groupId = Moodle\Domain\GroupId::fromInt($faker->numberBetween(1));

        $denormalized = Plugin\Domain\Room::create(
            Plugin\Domain\RoomId::fromInt($faker->numberBetween(1)),
            Plugin\Domain\ModuleId::fromInt($faker->numberBetween(1)),
            $groupId,
            Matrix\Domain\RoomId::fromString($faker->sha1()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
        );

        $roomNormalizer = new Plugin\Infrastructure\RoomNormalizer();

        $normalized = $roomNormalizer->normalize($denormalized);

        $expected = (object) [
            'group_id' => $groupId->toInt(),
            'id' => $denormalized->id()->toInt(),
            'module_id' => $denormalized->moduleId()->toInt(),
            'room_id' => $denormalized->matrixRoomId()->toString(),
            'timecreated' => $denormalized->timecreated()->toInt(),
            'timemodified' => $denormalized->timemodified()->toInt(),
        ];

        self::assertEquals($expected, $normalized);
    }
}
