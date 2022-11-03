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
 * @covers \mod_matrix\Plugin\Infrastructure\ModuleNormalizer
 *
 * @uses \mod_matrix\Moodle\Domain\CourseId
 * @uses \mod_matrix\Moodle\Domain\SectionId
 * @uses \mod_matrix\Moodle\Domain\Timestamp
 * @uses \mod_matrix\Plugin\Domain\Module
 * @uses \mod_matrix\Plugin\Domain\ModuleId
 * @uses \mod_matrix\Plugin\Domain\ModuleName
 * @uses \mod_matrix\Plugin\Domain\ModuleTarget
 * @uses \mod_matrix\Plugin\Domain\ModuleTopic
 * @uses \mod_matrix\Plugin\Domain\ModuleType
 */
final class ModuleNormalizerTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testDenormalizeReturnsModuleFromNormalizedModuleWhenNumericFieldsAreIntegers(): void
    {
        $faker = self::faker();

        $courseId = $faker->numberBetween(1);
        $id = $faker->numberBetween(1);
        $name = $faker->sentence();
        $topic = $faker->sentence();
        $target = $faker->randomElement([
            Plugin\Domain\ModuleTarget::elementUrl()->toString(),
            Plugin\Domain\ModuleTarget::matrixTo()->toString(),
        ]);
        $sectionId = $faker->numberBetween(1);
        $timecreated = $faker->dateTime()->getTimestamp();
        $timemodified = $faker->dateTime()->getTimestamp();
        $type = $faker->numberBetween(1);

        $normalized = (object) [
            'course' => $courseId,
            'id' => $id,
            'name' => $name,
            'topic' => $topic,
            'target' => $target,
            'section' => $sectionId,
            'timecreated' => $timecreated,
            'timemodified' => $timemodified,
            'type' => $type,
        ];

        $moduleNormalizer = new Plugin\Infrastructure\ModuleNormalizer();

        $denormalized = $moduleNormalizer->denormalize($normalized);

        $expected = Plugin\Domain\Module::create(
            Plugin\Domain\ModuleId::fromInt($id),
            Plugin\Domain\ModuleType::fromInt($type),
            Plugin\Domain\ModuleName::fromString($name),
            Plugin\Domain\ModuleTopic::fromString($topic),
            Plugin\Domain\ModuleTarget::fromString($target),
            Moodle\Domain\CourseId::fromInt($courseId),
            Moodle\Domain\SectionId::fromInt($sectionId),
            Moodle\Domain\Timestamp::fromInt($timecreated),
            Moodle\Domain\Timestamp::fromInt($timemodified),
        );

        self::assertEquals($expected, $denormalized);
    }

    public function testDenormalizeReturnsModuleFromNormalizedModuleWhenNumericFieldsAreStrings(): void
    {
        $faker = self::faker();

        $courseId = (string) $faker->numberBetween(1);
        $sectionId = (string) $faker->numberBetween(1);
        $id = (string) $faker->numberBetween(1);
        $name = $faker->sentence();
        $topic = $faker->sentence();
        $target = $faker->randomElement([
            Plugin\Domain\ModuleTarget::elementUrl()->toString(),
            Plugin\Domain\ModuleTarget::matrixTo()->toString(),
        ]);
        $timecreated = (string) $faker->dateTime()->getTimestamp();
        $timemodified = (string) $faker->dateTime()->getTimestamp();
        $type = (string) $faker->numberBetween(1);

        $normalized = (object) [
            'course' => $courseId,
            'id' => $id,
            'name' => $name,
            'topic' => $topic,
            'target' => $target,
            'section' => $sectionId,
            'timecreated' => $timecreated,
            'timemodified' => $timemodified,
            'type' => $type,
        ];

        $moduleNormalizer = new Plugin\Infrastructure\ModuleNormalizer();

        $denormalized = $moduleNormalizer->denormalize($normalized);

        $expected = Plugin\Domain\Module::create(
            Plugin\Domain\ModuleId::fromString($id),
            Plugin\Domain\ModuleType::fromString($type),
            Plugin\Domain\ModuleName::fromString($name),
            Plugin\Domain\ModuleTopic::fromString($topic),
            Plugin\Domain\ModuleTarget::fromString($target),
            Moodle\Domain\CourseId::fromString($courseId),
            Moodle\Domain\SectionId::fromString($sectionId),
            Moodle\Domain\Timestamp::fromString($timecreated),
            Moodle\Domain\Timestamp::fromString($timemodified),
        );

        self::assertEquals($expected, $denormalized);
    }

    public function testDenormalizeReturnsModuleFromNormalizedModuleWhenTopicIsNull(): void
    {
        $faker = self::faker();

        $courseId = $faker->numberBetween(1);
        $id = $faker->numberBetween(1);
        $name = $faker->sentence();
        $topic = null;
        $target = $faker->randomElement([
            Plugin\Domain\ModuleTarget::elementUrl()->toString(),
            Plugin\Domain\ModuleTarget::matrixTo()->toString(),
        ]);
        $sectionId = $faker->numberBetween(1);
        $timecreated = $faker->dateTime()->getTimestamp();
        $timemodified = $faker->dateTime()->getTimestamp();
        $type = $faker->numberBetween(1);

        $normalized = (object) [
            'course' => $courseId,
            'id' => $id,
            'name' => $name,
            'topic' => $topic,
            'target' => $target,
            'section' => $sectionId,
            'timecreated' => $timecreated,
            'timemodified' => $timemodified,
            'type' => $type,
        ];

        $moduleNormalizer = new Plugin\Infrastructure\ModuleNormalizer();

        $denormalized = $moduleNormalizer->denormalize($normalized);

        $expected = Plugin\Domain\Module::create(
            Plugin\Domain\ModuleId::fromInt($id),
            Plugin\Domain\ModuleType::fromInt($type),
            Plugin\Domain\ModuleName::fromString($name),
            Plugin\Domain\ModuleTopic::fromString(''),
            Plugin\Domain\ModuleTarget::fromString($target),
            Moodle\Domain\CourseId::fromInt($courseId),
            Moodle\Domain\SectionId::fromInt($sectionId),
            Moodle\Domain\Timestamp::fromInt($timecreated),
            Moodle\Domain\Timestamp::fromInt($timemodified),
        );

        self::assertEquals($expected, $denormalized);
    }

    public function testNormalizeReturnsModuleFromDenormalizedModuleWhenTopicIsEmptyString(): void
    {
        $faker = self::faker();

        $denormalized = Plugin\Domain\Module::create(
            Plugin\Domain\ModuleId::fromInt($faker->numberBetween(1)),
            Plugin\Domain\ModuleType::fromInt($faker->numberBetween(1)),
            Plugin\Domain\ModuleName::fromString($faker->sentence()),
            Plugin\Domain\ModuleTopic::fromString(''),
            Plugin\Domain\ModuleTarget::elementUrl(),
            Moodle\Domain\CourseId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\SectionId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
        );

        $moduleNormalizer = new Plugin\Infrastructure\ModuleNormalizer();

        $normalized = $moduleNormalizer->normalize($denormalized);

        $expected = (object) [
            'course' => $denormalized->courseId()->toInt(),
            'id' => $denormalized->id()->toInt(),
            'name' => $denormalized->name()->toString(),
            'topic' => null,
            'target' => $denormalized->target()->toString(),
            'section' => $denormalized->sectionId()->toInt(),
            'timecreated' => $denormalized->timecreated()->toInt(),
            'timemodified' => $denormalized->timemodified()->toInt(),
            'type' => $denormalized->type()->toInt(),
        ];

        self::assertEquals($expected, $normalized);
    }

    public function testNormalizeReturnsModuleFromDenormalizedModuleWhenTopicIsNotAnEmptyString(): void
    {
        $faker = self::faker();

        $denormalized = Plugin\Domain\Module::create(
            Plugin\Domain\ModuleId::fromInt($faker->numberBetween(1)),
            Plugin\Domain\ModuleType::fromInt($faker->numberBetween(1)),
            Plugin\Domain\ModuleName::fromString($faker->sentence()),
            Plugin\Domain\ModuleTopic::fromString($faker->sentence()),
            Plugin\Domain\ModuleTarget::elementUrl(),
            Moodle\Domain\CourseId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\SectionId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
        );

        $moduleNormalizer = new Plugin\Infrastructure\ModuleNormalizer();

        $normalized = $moduleNormalizer->normalize($denormalized);

        $expected = (object) [
            'course' => $denormalized->courseId()->toInt(),
            'id' => $denormalized->id()->toInt(),
            'name' => $denormalized->name()->toString(),
            'topic' => $denormalized->topic()->toString(),
            'target' => $denormalized->target()->toString(),
            'section' => $denormalized->sectionId()->toInt(),
            'timecreated' => $denormalized->timecreated()->toInt(),
            'timemodified' => $denormalized->timemodified()->toInt(),
            'type' => $denormalized->type()->toInt(),
        ];

        self::assertEquals($expected, $normalized);
    }
}
