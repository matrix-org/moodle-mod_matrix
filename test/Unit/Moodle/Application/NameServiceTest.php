<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Moodle\Application;

use mod_matrix\Moodle;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Moodle\Application\NameService
 *
 * @uses \mod_matrix\Moodle\Domain\Course
 * @uses \mod_matrix\Moodle\Domain\CourseFullName
 * @uses \mod_matrix\Moodle\Domain\CourseId
 * @uses \mod_matrix\Moodle\Domain\CourseShortName
 * @uses \mod_matrix\Moodle\Domain\Group
 * @uses \mod_matrix\Moodle\Domain\GroupId
 * @uses \mod_matrix\Moodle\Domain\GroupName
 * @uses \mod_matrix\Moodle\Domain\Module
 * @uses \mod_matrix\Moodle\Domain\ModuleId
 * @uses \mod_matrix\Moodle\Domain\ModuleName
 * @uses \mod_matrix\Moodle\Domain\ModuleType
 * @uses \mod_matrix\Moodle\Domain\SectionId
 * @uses \mod_matrix\Moodle\Domain\Timestamp
 */
final class NameServiceTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateForGroupCourseAndModuleReturnsName(): void
    {
        $faker = self::faker();

        $group = Moodle\Domain\Group::create(
            Moodle\Domain\GroupId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\GroupName::fromString($faker->word()),
        );

        $course = Moodle\Domain\Course::create(
            Moodle\Domain\CourseId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\CourseFullName::fromString($faker->sentence()),
            Moodle\Domain\CourseShortName::fromString($faker->word()),
        );

        $module = Moodle\Domain\Module::create(
            Moodle\Domain\ModuleId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\ModuleType::fromInt($faker->numberBetween(1)),
            Moodle\Domain\ModuleName::fromString($faker->sentence()),
            Moodle\Domain\CourseId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\SectionId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
        );

        $nameService = new Moodle\Application\NameService();

        $name = $nameService->createForGroupCourseAndModule(
            $group,
            $course,
            $module,
        );

        $expected = \sprintf(
            '%s: %s (%s)',
            $group->name()->toString(),
            $course->fullName()->toString(),
            $module->name()->toString(),
        );

        self::assertSame($expected, $name);
    }

    public function testCreateForCourseAndModuleReturnsName(): void
    {
        $faker = self::faker();

        $course = Moodle\Domain\Course::create(
            Moodle\Domain\CourseId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\CourseFullName::fromString($faker->sentence()),
            Moodle\Domain\CourseShortName::fromString($faker->word()),
        );

        $module = Moodle\Domain\Module::create(
            Moodle\Domain\ModuleId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\ModuleType::fromInt($faker->numberBetween(1)),
            Moodle\Domain\ModuleName::fromString($faker->sentence()),
            Moodle\Domain\CourseId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\SectionId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp()),
        );

        $nameService = new Moodle\Application\NameService();

        $name = $nameService->createForCourseAndModule(
            $course,
            $module,
        );

        $expected = \sprintf(
            '%s (%s)',
            $course->fullName()->toString(),
            $module->name()->toString(),
        );

        self::assertSame($expected, $name);
    }
}
