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
 * @covers \mod_matrix\Moodle\Infrastructure\ModuleNormalizer
 *
 * @uses \mod_matrix\Moodle\Domain\CourseId
 * @uses \mod_matrix\Moodle\Domain\Module
 * @uses \mod_matrix\Moodle\Domain\ModuleId
 * @uses \mod_matrix\Moodle\Domain\Name
 * @uses \mod_matrix\Moodle\Domain\Timestamp
 * @uses \mod_matrix\Moodle\Domain\Type
 */
final class ModuleNormalizerTest extends Framework\TestCase
{
    use Util\Helper;

    public function testDenormalizeReturnsModuleFromNormalizedModuleWhenNumericFieldsAreIntegers(): void
    {
        $faker = self::faker();

        $courseId = $faker->numberBetween(1);
        $id = $faker->numberBetween(1);
        $name = $faker->sentence();
        $sectionId = $faker->numberBetween(1);
        $timecreated = $faker->dateTime()->getTimestamp();
        $timemodified = $faker->dateTime()->getTimestamp();
        $type = $faker->numberBetween(1);

        $normalized = (object) [
            'course' => $courseId,
            'id' => $id,
            'name' => $name,
            'section' => $sectionId,
            'timecreated' => $timecreated,
            'timemodified' => $timemodified,
            'type' => $type,
        ];

        $moduleNormalizer = new Moodle\Infrastructure\ModuleNormalizer();

        $denormalized = $moduleNormalizer->denormalize($normalized);

        $expected = Moodle\Domain\Module::create(
            Moodle\Domain\ModuleId::fromInt($id),
            Moodle\Domain\Type::fromInt($type),
            Moodle\Domain\Name::fromString($name),
            Moodle\Domain\CourseId::fromInt($courseId),
            Moodle\Domain\SectionId::fromInt($sectionId),
            Moodle\Domain\Timestamp::fromInt($timecreated),
            Moodle\Domain\Timestamp::fromInt($timemodified)
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
        $timecreated = (string) $faker->dateTime()->getTimestamp();
        $timemodified = (string) $faker->dateTime()->getTimestamp();
        $type = (string) $faker->numberBetween(1);

        $normalized = (object) [
            'course' => $courseId,
            'id' => $id,
            'name' => $name,
            'section' => $sectionId,
            'timecreated' => $timecreated,
            'timemodified' => $timemodified,
            'type' => $type,
        ];

        $moduleNormalizer = new Moodle\Infrastructure\ModuleNormalizer();

        $denormalized = $moduleNormalizer->denormalize($normalized);

        $expected = Moodle\Domain\Module::create(
            Moodle\Domain\ModuleId::fromString($id),
            Moodle\Domain\Type::fromString($type),
            Moodle\Domain\Name::fromString($name),
            Moodle\Domain\CourseId::fromString($courseId),
            Moodle\Domain\SectionId::fromString($sectionId),
            Moodle\Domain\Timestamp::fromString($timecreated),
            Moodle\Domain\Timestamp::fromString($timemodified)
        );

        self::assertEquals($expected, $denormalized);
    }

    public function testNormalizeReturnsModuleFromDenormalizedModule(): void
    {
        $faker = self::faker();

        $denormalized = Moodle\Domain\Module::create(
            Moodle\Domain\ModuleId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\Type::fromInt($faker->numberBetween(1)),
            Moodle\Domain\Name::fromString($faker->sentence()),
            Moodle\Domain\CourseId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\SectionId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp())
        );

        $moduleNormalizer = new Moodle\Infrastructure\ModuleNormalizer();

        $normalized = $moduleNormalizer->normalize($denormalized);

        $expected = (object) [
            'course' => $denormalized->courseId()->toInt(),
            'id' => $denormalized->id()->toInt(),
            'name' => $denormalized->name()->toString(),
            'section' => $denormalized->sectionId()->toInt(),
            'timecreated' => $denormalized->timecreated()->toInt(),
            'timemodified' => $denormalized->timemodified()->toInt(),
            'type' => $denormalized->type()->toInt(),
        ];

        self::assertEquals($expected, $normalized);
    }
}
