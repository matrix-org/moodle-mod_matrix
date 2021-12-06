<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Moodle\Domain;

use mod_matrix\Moodle;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Moodle\Domain\Module
 *
 * @uses \mod_matrix\Moodle\Domain\CourseId
 * @uses \mod_matrix\Moodle\Domain\ModuleId
 * @uses \mod_matrix\Moodle\Domain\ModuleName
 * @uses \mod_matrix\Moodle\Domain\ModuleTopic
 * @uses \mod_matrix\Moodle\Domain\ModuleType
 * @uses \mod_matrix\Moodle\Domain\SectionId
 * @uses \mod_matrix\Moodle\Domain\Timestamp
 */
final class ModuleTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsModule(): void
    {
        $faker = self::faker();

        $id = Moodle\Domain\ModuleId::fromInt($faker->numberBetween(1));
        $type = Moodle\Domain\ModuleType::fromInt($faker->numberBetween(1));
        $name = Moodle\Domain\ModuleName::fromString($faker->sentence());
        $topic = Moodle\Domain\ModuleTopic::fromString($faker->sentence());
        $courseId = Moodle\Domain\CourseId::fromInt($faker->numberBetween(1));
        $sectionId = Moodle\Domain\SectionId::fromInt($faker->numberBetween(1));
        $timecreated = Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp());
        $timemodified = Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp());

        $module = Moodle\Domain\Module::create(
            $id,
            $type,
            $name,
            $topic,
            $courseId,
            $sectionId,
            $timecreated,
            $timemodified,
        );

        self::assertSame($id, $module->id());
        self::assertSame($type, $module->type());
        self::assertSame($name, $module->name());
        self::assertSame($topic, $module->topic());
        self::assertSame($courseId, $module->courseId());
        self::assertSame($sectionId, $module->sectionId());
        self::assertSame($timemodified, $module->timemodified());
        self::assertSame($timecreated, $module->timecreated());
    }
}
