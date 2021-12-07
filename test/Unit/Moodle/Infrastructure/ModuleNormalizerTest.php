<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Moodle\Infrastructure;

use mod_matrix\Moodle;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Moodle\Infrastructure\ModuleNormalizer
 *
 * @uses \mod_matrix\Moodle\Domain\CourseId
 * @uses \mod_matrix\Moodle\Domain\Module
 * @uses \mod_matrix\Moodle\Domain\ModuleId
 * @uses \mod_matrix\Moodle\Domain\ModuleName
 * @uses \mod_matrix\Moodle\Domain\ModuleTarget
 * @uses \mod_matrix\Moodle\Domain\ModuleTopic
 * @uses \mod_matrix\Moodle\Domain\ModuleType
 * @uses \mod_matrix\Moodle\Domain\SectionId
 * @uses \mod_matrix\Moodle\Domain\Timestamp
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
            Moodle\Domain\ModuleTarget::elementUrl()->toString(),
            Moodle\Domain\ModuleTarget::matrixTo()->toString(),
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

        $moduleNormalizer = new Moodle\Infrastructure\ModuleNormalizer();

        $denormalized = $moduleNormalizer->denormalize($normalized);

        $expected = Moodle\Domain\Module::create(
            Moodle\Domain\ModuleId::fromInt($id),
            Moodle\Domain\ModuleType::fromInt($type),
            Moodle\Domain\ModuleName::fromString($name),
            Moodle\Domain\ModuleTopic::fromString($topic),
            Moodle\Domain\ModuleTarget::fromString($target),
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
            Moodle\Domain\ModuleTarget::elementUrl()->toString(),
            Moodle\Domain\ModuleTarget::matrixTo()->toString(),
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

        $moduleNormalizer = new Moodle\Infrastructure\ModuleNormalizer();

        $denormalized = $moduleNormalizer->denormalize($normalized);

        $expected = Moodle\Domain\Module::create(
            Moodle\Domain\ModuleId::fromString($id),
            Moodle\Domain\ModuleType::fromString($type),
            Moodle\Domain\ModuleName::fromString($name),
            Moodle\Domain\ModuleTopic::fromString($topic),
            Moodle\Domain\ModuleTarget::fromString($target),
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
            Moodle\Domain\ModuleTarget::elementUrl()->toString(),
            Moodle\Domain\ModuleTarget::matrixTo()->toString(),
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

        $moduleNormalizer = new Moodle\Infrastructure\ModuleNormalizer();

        $denormalized = $moduleNormalizer->denormalize($normalized);

        $expected = Moodle\Domain\Module::create(
            Moodle\Domain\ModuleId::fromInt($id),
            Moodle\Domain\ModuleType::fromInt($type),
            Moodle\Domain\ModuleName::fromString($name),
            Moodle\Domain\ModuleTopic::fromString(''),
            Moodle\Domain\ModuleTarget::fromString($target),
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

        $denormalized = Moodle\Domain\Module::create(
            Moodle\Domain\ModuleId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\ModuleType::fromInt($faker->numberBetween(1)),
            Moodle\Domain\ModuleName::fromString($faker->sentence()),
            Moodle\Domain\ModuleTopic::fromString(''),
            Moodle\Domain\ModuleTarget::elementUrl(),
            Moodle\Domain\CourseId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\SectionId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
        );

        $moduleNormalizer = new Moodle\Infrastructure\ModuleNormalizer();

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

        $denormalized = Moodle\Domain\Module::create(
            Moodle\Domain\ModuleId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\ModuleType::fromInt($faker->numberBetween(1)),
            Moodle\Domain\ModuleName::fromString($faker->sentence()),
            Moodle\Domain\ModuleTopic::fromString($faker->sentence()),
            Moodle\Domain\ModuleTarget::elementUrl(),
            Moodle\Domain\CourseId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\SectionId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
        );

        $moduleNormalizer = new Moodle\Infrastructure\ModuleNormalizer();

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
