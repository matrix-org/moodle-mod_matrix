<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Test\Unit\Plugin\Application;

use Ergebnis\Clock;
use mod_matrix\Moodle;
use mod_matrix\Plugin;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Plugin\Application\ModuleService
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
final class ModuleServiceTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateCreatesAndReturnsModule(): void
    {
        $faker = self::faker();

        $name = Plugin\Domain\ModuleName::fromString($faker->sentence());
        $topic = Plugin\Domain\ModuleTopic::fromString($faker->sentence());
        $target = Plugin\Domain\ModuleTarget::matrixTo();
        $courseId = Moodle\Domain\CourseId::fromInt($faker->numberBetween(1));
        $sectionId = Moodle\Domain\SectionId::fromInt($faker->numberBetween(1));

        $now = \DateTimeImmutable::createFromMutable($faker->dateTime());

        $expectedModule = null;

        $moduleRepository = $this->createMock(Plugin\Domain\ModuleRepository::class);

        $moduleRepository
            ->expects(self::once())
            ->method('save')
            ->with(self::callback(static function (Plugin\Domain\Module $module) use ($name, $topic, $target, $courseId, $sectionId, $now, &$expectedModule): bool {
                self::assertEquals(Plugin\Domain\ModuleId::unknown(), $module->id());
                self::assertEquals(Plugin\Domain\ModuleType::fromInt(0), $module->type());
                self::assertSame($name, $module->name());
                self::assertSame($topic, $module->topic());
                self::assertSame($target, $module->target());
                self::assertSame($courseId, $module->courseId());
                self::assertSame($sectionId, $module->sectionId());
                self::assertEquals(Moodle\Domain\Timestamp::fromInt($now->getTimestamp()), $module->timecreated());
                self::assertEquals(Moodle\Domain\Timestamp::fromInt(0), $module->timemodified());

                $expectedModule = $module;

                return true;
            }));

        $moduleService = new Plugin\Application\ModuleService(
            $moduleRepository,
            new Clock\FrozenClock($now),
        );

        $module = $moduleService->create(
            $name,
            $topic,
            $target,
            $courseId,
            $sectionId,
        );

        self::assertSame($module, $expectedModule);
    }
}
