<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Matrix\Domain;

use Ergebnis\Test\Util;
use mod_matrix\Matrix;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Matrix\Domain\Module
 *
 * @uses \mod_matrix\Matrix\Domain\CourseId
 * @uses \mod_matrix\Matrix\Domain\ModuleId
 * @uses \mod_matrix\Matrix\Domain\Name
 * @uses \mod_matrix\Matrix\Domain\Timestamp
 * @uses \mod_matrix\Matrix\Domain\Type
 */
final class ModuleTest extends Framework\TestCase
{
    use Util\Helper;

    public function testCreateReturnsModule(): void
    {
        $faker = self::faker();

        $moduleId = Matrix\Domain\ModuleId::fromInt($faker->numberBetween(1));
        $type = Matrix\Domain\Type::fromInt($faker->numberBetween(1));
        $name = Matrix\Domain\Name::fromString($faker->sentence());
        $courseId = Matrix\Domain\CourseId::fromInt($faker->numberBetween(1));
        $timecreated = Matrix\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp());
        $timemodified = Matrix\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp());

        $module = Matrix\Domain\Module::create(
            $moduleId,
            $type,
            $name,
            $courseId,
            $timecreated,
            $timemodified
        );

        self::assertSame($moduleId, $module->id());
        self::assertSame($type, $module->type());
        self::assertSame($name, $module->name());
        self::assertSame($courseId, $module->courseId());
        self::assertSame($timemodified, $module->timemodified());
        self::assertSame($timecreated, $module->timecreated());
    }
}
