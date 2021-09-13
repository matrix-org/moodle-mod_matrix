<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Matrix\Infrastructure;

use Ergebnis\Test\Util;
use mod_matrix\Matrix;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Matrix\Infrastructure\ModuleNormalizer
 *
 * @uses \mod_matrix\Matrix\Domain\CourseId
 * @uses \mod_matrix\Matrix\Domain\Module
 * @uses \mod_matrix\Matrix\Domain\ModuleId
 * @uses \mod_matrix\Matrix\Domain\Name
 * @uses \mod_matrix\Matrix\Domain\Timestamp
 * @uses \mod_matrix\Matrix\Domain\Type
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
        $timecreated = $faker->dateTime()->getTimestamp();
        $timemodified = $faker->dateTime()->getTimestamp();
        $type = $faker->numberBetween(1);

        $normalized = (object) [
            'course' => $courseId,
            'id' => $id,
            'name' => $name,
            'timecreated' => $timecreated,
            'timemodified' => $timemodified,
            'type' => $type,
        ];

        $moduleNormalizer = new Matrix\Infrastructure\ModuleNormalizer();

        $denormalized = $moduleNormalizer->denormalize($normalized);

        $expected = Matrix\Domain\Module::create(
            Matrix\Domain\ModuleId::fromInt($id),
            Matrix\Domain\Type::fromInt($type),
            Matrix\Domain\Name::fromString($name),
            Matrix\Domain\CourseId::fromInt($courseId),
            Matrix\Domain\Timestamp::fromInt($timecreated),
            Matrix\Domain\Timestamp::fromInt($timemodified)
        );

        self::assertEquals($expected, $denormalized);
    }

    public function testDenormalizeReturnsModuleFromNormalizedModuleWhenNumericFieldsAreStrings(): void
    {
        $faker = self::faker();

        $courseId = (string) $faker->numberBetween(1);
        $id = (string) $faker->numberBetween(1);
        $name = $faker->sentence();
        $timecreated = (string) $faker->dateTime()->getTimestamp();
        $timemodified = (string) $faker->dateTime()->getTimestamp();
        $type = (string) $faker->numberBetween(1);

        $normalized = (object) [
            'course' => $courseId,
            'id' => $id,
            'name' => $name,
            'timecreated' => $timecreated,
            'timemodified' => $timemodified,
            'type' => $type,
        ];

        $moduleNormalizer = new Matrix\Infrastructure\ModuleNormalizer();

        $denormalized = $moduleNormalizer->denormalize($normalized);

        $expected = Matrix\Domain\Module::create(
            Matrix\Domain\ModuleId::fromString($id),
            Matrix\Domain\Type::fromString($type),
            Matrix\Domain\Name::fromString($name),
            Matrix\Domain\CourseId::fromString($courseId),
            Matrix\Domain\Timestamp::fromString($timecreated),
            Matrix\Domain\Timestamp::fromString($timemodified)
        );

        self::assertEquals($expected, $denormalized);
    }

    public function testNormalizeReturnsModuleFromDenormalizedModule(): void
    {
        $faker = self::faker();

        $denormalized = Matrix\Domain\Module::create(
            Matrix\Domain\ModuleId::fromInt($faker->numberBetween(1)),
            Matrix\Domain\Type::fromInt($faker->numberBetween(1)),
            Matrix\Domain\Name::fromString($faker->sentence()),
            Matrix\Domain\CourseId::fromInt($faker->numberBetween(1)),
            Matrix\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
            Matrix\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp())
        );

        $moduleNormalizer = new Matrix\Infrastructure\ModuleNormalizer();

        $normalized = $moduleNormalizer->normalize($denormalized);

        $expected = (object) [
            'course' => $denormalized->courseId()->toInt(),
            'id' => $denormalized->id()->toInt(),
            'name' => $denormalized->name()->toString(),
            'timecreated' => $denormalized->timecreated()->toInt(),
            'timemodified' => $denormalized->timemodified()->toInt(),
            'type' => $denormalized->type()->toInt(),
        ];

        self::assertEquals($expected, $normalized);
    }
}
