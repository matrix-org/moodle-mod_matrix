<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
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
 * @covers \mod_matrix\Plugin\Domain\Module
 *
 * @uses \mod_matrix\Moodle\Domain\CourseId
 * @uses \mod_matrix\Moodle\Domain\SectionId
 * @uses \mod_matrix\Moodle\Domain\Timestamp
 * @uses \mod_matrix\Plugin\Domain\ModuleId
 * @uses \mod_matrix\Plugin\Domain\ModuleName
 * @uses \mod_matrix\Plugin\Domain\ModuleTarget
 * @uses \mod_matrix\Plugin\Domain\ModuleTopic
 * @uses \mod_matrix\Plugin\Domain\ModuleType
 */
final class ModuleTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsModule(): void
    {
        $faker = self::faker();

        $id = Plugin\Domain\ModuleId::fromInt($faker->numberBetween(1));
        $type = Plugin\Domain\ModuleType::fromInt($faker->numberBetween(1));
        $name = Plugin\Domain\ModuleName::fromString($faker->sentence());
        $topic = Plugin\Domain\ModuleTopic::fromString($faker->sentence());
        $target = Plugin\Domain\ModuleTarget::elementUrl();
        $courseId = Moodle\Domain\CourseId::fromInt($faker->numberBetween(1));
        $sectionId = Moodle\Domain\SectionId::fromInt($faker->numberBetween(1));
        $timecreated = Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp());
        $timemodified = Moodle\Domain\Timestamp::fromInt($faker->dateTime->getTimestamp());

        $module = Plugin\Domain\Module::create(
            $id,
            $type,
            $name,
            $topic,
            $target,
            $courseId,
            $sectionId,
            $timecreated,
            $timemodified,
        );

        self::assertSame($id, $module->id());
        self::assertSame($type, $module->type());
        self::assertSame($name, $module->name());
        self::assertSame($topic, $module->topic());
        self::assertSame($target, $module->target());
        self::assertSame($courseId, $module->courseId());
        self::assertSame($sectionId, $module->sectionId());
        self::assertSame($timemodified, $module->timemodified());
        self::assertSame($timecreated, $module->timecreated());
    }
}
