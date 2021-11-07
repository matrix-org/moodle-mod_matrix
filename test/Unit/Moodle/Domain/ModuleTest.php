<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Moodle\Domain;

use Ergebnis\Test\Util;
use mod_matrix\Moodle;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Moodle\Domain\Module
 *
 * @uses \mod_matrix\Moodle\Domain\CourseId
 * @uses \mod_matrix\Moodle\Domain\ModuleId
 * @uses \mod_matrix\Moodle\Domain\Name
 * @uses \mod_matrix\Moodle\Domain\SectionId
 * @uses \mod_matrix\Moodle\Domain\Timestamp
 * @uses \mod_matrix\Moodle\Domain\Type
 */
final class ModuleTest extends Framework\TestCase
{
    use Util\Helper;

    public function testCreateReturnsModule(): void
    {
        $faker = self::faker();

        $id = Moodle\Domain\ModuleId::fromInt($faker->numberBetween(1));
        $type = Moodle\Domain\Type::fromInt($faker->numberBetween(1));
        $name = Moodle\Domain\Name::fromString($faker->sentence());
        $courseId = Moodle\Domain\CourseId::fromInt($faker->numberBetween(1));
        $sectionId = Moodle\Domain\SectionId::fromInt($faker->numberBetween(1));
        $timecreated = Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp());
        $timemodified = Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp());

        $module = Moodle\Domain\Module::create(
            $id,
            $type,
            $name,
            $courseId,
            $sectionId,
            $timecreated,
            $timemodified
        );

        self::assertSame($id, $module->id());
        self::assertSame($type, $module->type());
        self::assertSame($name, $module->name());
        self::assertSame($courseId, $module->courseId());
        self::assertSame($sectionId, $module->sectionId());
        self::assertSame($timemodified, $module->timemodified());
        self::assertSame($timecreated, $module->timecreated());
    }
}
