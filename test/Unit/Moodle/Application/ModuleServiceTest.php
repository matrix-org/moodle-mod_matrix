<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Moodle\Application;

use Ergebnis\Clock;
use mod_matrix\Moodle;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Moodle\Application\ModuleService
 *
 * @uses \mod_matrix\Moodle\Domain\CourseId
 * @uses \mod_matrix\Moodle\Domain\Module
 * @uses \mod_matrix\Moodle\Domain\ModuleId
 * @uses \mod_matrix\Moodle\Domain\ModuleName
 * @uses \mod_matrix\Moodle\Domain\ModuleType
 * @uses \mod_matrix\Moodle\Domain\SectionId
 * @uses \mod_matrix\Moodle\Domain\Timestamp
 */
final class ModuleServiceTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateCreatesAndReturnsModule(): void
    {
        $faker = self::faker();

        $name = Moodle\Domain\ModuleName::fromString($faker->sentence());
        $courseId = Moodle\Domain\CourseId::fromInt($faker->numberBetween(1));
        $sectionId = Moodle\Domain\SectionId::fromInt($faker->numberBetween(1));

        $now = \DateTimeImmutable::createFromMutable($faker->dateTime());

        $expectedModule = null;

        $moduleRepository = $this->createMock(Moodle\Domain\ModuleRepository::class);

        $moduleRepository
            ->expects(self::once())
            ->method('save')
            ->with(self::callback(static function (Moodle\Domain\Module $module) use ($name, $courseId, $sectionId, $now, &$expectedModule): bool {
                self::assertEquals(Moodle\Domain\ModuleId::unknown(), $module->id());
                self::assertEquals(Moodle\Domain\ModuleType::fromInt(0), $module->type());
                self::assertSame($name, $module->name());
                self::assertSame($courseId, $module->courseId());
                self::assertSame($sectionId, $module->sectionId());
                self::assertEquals(Moodle\Domain\Timestamp::fromInt($now->getTimestamp()), $module->timecreated());
                self::assertEquals(Moodle\Domain\Timestamp::fromInt(0), $module->timemodified());

                $expectedModule = $module;

                return true;
            }));

        $moduleService = new Moodle\Application\ModuleService(
            $moduleRepository,
            new Clock\FrozenClock($now),
        );

        $module = $moduleService->create(
            $name,
            $courseId,
            $sectionId,
        );

        self::assertSame($module, $expectedModule);
    }
}
