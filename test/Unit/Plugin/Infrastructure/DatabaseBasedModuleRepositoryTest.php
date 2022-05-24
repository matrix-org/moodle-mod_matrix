<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Test\Unit\Plugin\Infrastructure;

use mod_matrix\Matrix;
use mod_matrix\Moodle;
use mod_matrix\Plugin;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Plugin\Infrastructure\DatabaseBasedModuleRepository
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
 * @uses \mod_matrix\Plugin\Infrastructure\ModuleNormalizer
 */
final class DatabaseBasedModuleRepositoryTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testConstants(): void
    {
        self::assertSame('matrix', Plugin\Infrastructure\DatabaseBasedModuleRepository::TABLE);
    }

    public function testFindOneByReturnsNullWhenModuleCouldNotBeFound(): void
    {
        $faker = self::faker()->unique();

        $conditions = [
            'id' => $faker->numberBetween(1),
        ];

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('get_record')
            ->with(
                self::identicalTo(Plugin\Infrastructure\DatabaseBasedModuleRepository::TABLE),
                self::identicalTo($conditions),
                self::identicalTo('*'),
                self::identicalTo(IGNORE_MISSING),
            )
            ->willReturn(null);

        $moduleRepository = new Plugin\Infrastructure\DatabaseBasedModuleRepository(
            $database,
            new Plugin\Infrastructure\ModuleNormalizer(),
        );

        self::assertNull($moduleRepository->findOneBy($conditions));
    }

    public function testFindOneByReturnsModuleWhenModuleCouldBeFound(): void
    {
        $faker = self::faker();

        $id = $faker->numberBetween(1);

        $conditions = [
            'id' => $id,
        ];

        $normalized = (object) [
            'course' => $faker->numberBetween(1),
            'id' => $id,
            'name' => $faker->sentence(),
            'section' => $faker->numberBetween(1),
            'target' => $faker->randomElement([
                Plugin\Domain\ModuleTarget::elementUrl()->toString(),
                Plugin\Domain\ModuleTarget::matrixTo()->toString(),
            ]),
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
            'topic' => $faker->sentence(),
            'type' => $faker->numberBetween(1),
        ];

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('get_record')
            ->with(
                self::identicalTo(Plugin\Infrastructure\DatabaseBasedModuleRepository::TABLE),
                self::identicalTo($conditions),
                self::identicalTo('*'),
                self::identicalTo(IGNORE_MISSING),
            )
            ->willReturn($normalized);

        $moduleRepository = new Plugin\Infrastructure\DatabaseBasedModuleRepository(
            $database,
            new Plugin\Infrastructure\ModuleNormalizer(),
        );

        $expected = Plugin\Domain\Module::create(
            Plugin\Domain\ModuleId::fromString((string) $normalized->id),
            Plugin\Domain\ModuleType::fromString((string) $normalized->type),
            Plugin\Domain\ModuleName::fromString((string) $normalized->name),
            Plugin\Domain\ModuleTopic::fromString((string) $normalized->topic),
            Plugin\Domain\ModuleTarget::fromString((string) $normalized->target),
            Moodle\Domain\CourseId::fromString((string) $normalized->course),
            Moodle\Domain\SectionId::fromString((string) $normalized->section),
            Moodle\Domain\Timestamp::fromString((string) $normalized->timecreated),
            Moodle\Domain\Timestamp::fromString((string) $normalized->timemodified),
        );

        self::assertEquals($expected, $moduleRepository->findOneBy($conditions));
    }

    public function testFindAllByReturnsModulesForConditions(): void
    {
        $faker = self::faker();

        $courseId = $faker->numberBetween(1);

        $conditions = [
            'course' => $courseId,
        ];

        $one = (object) [
            'course' => $courseId,
            'id' => $faker->numberBetween(1),
            'name' => $faker->sentence(),
            'section' => $faker->numberBetween(1),
            'target' => Plugin\Domain\ModuleTarget::matrixTo()->toString(),
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
            'topic' => $faker->sentence(),
            'type' => $faker->numberBetween(1),
        ];

        $two = (object) [
            'course' => $courseId,
            'id' => $faker->numberBetween(1),
            'name' => $faker->sentence(),
            'section' => $faker->numberBetween(1),
            'target' => Plugin\Domain\ModuleTarget::elementUrl()->toString(),
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
            'topic' => $faker->sentence(),
            'type' => $faker->numberBetween(1),
        ];

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('get_records')
            ->with(
                self::identicalTo(Plugin\Infrastructure\DatabaseBasedModuleRepository::TABLE),
                self::identicalTo($conditions),
            )
            ->willReturn([
                $one,
                $two,
            ]);

        $moduleRepository = new Plugin\Infrastructure\DatabaseBasedModuleRepository(
            $database,
            new Plugin\Infrastructure\ModuleNormalizer(),
        );

        $expected = [
            Plugin\Domain\Module::create(
                Plugin\Domain\ModuleId::fromString((string) $one->id),
                Plugin\Domain\ModuleType::fromString((string) $one->type),
                Plugin\Domain\ModuleName::fromString((string) $one->name),
                Plugin\Domain\ModuleTopic::fromString((string) $one->topic),
                Plugin\Domain\ModuleTarget::matrixTo(),
                Moodle\Domain\CourseId::fromString((string) $one->course),
                Moodle\Domain\SectionId::fromString((string) $one->section),
                Moodle\Domain\Timestamp::fromString((string) $one->timecreated),
                Moodle\Domain\Timestamp::fromString((string) $one->timemodified),
            ),
            Plugin\Domain\Module::create(
                Plugin\Domain\ModuleId::fromString((string) $two->id),
                Plugin\Domain\ModuleType::fromString((string) $two->type),
                Plugin\Domain\ModuleName::fromString((string) $two->name),
                Plugin\Domain\ModuleTopic::fromString((string) $two->topic),
                Plugin\Domain\ModuleTarget::elementUrl(),
                Moodle\Domain\CourseId::fromString((string) $two->course),
                Moodle\Domain\SectionId::fromString((string) $two->section),
                Moodle\Domain\Timestamp::fromString((string) $two->timecreated),
                Moodle\Domain\Timestamp::fromString((string) $two->timemodified),
            ),
        ];

        self::assertEquals($expected, $moduleRepository->findAllBy($conditions));
    }

    public function testSaveInsertsRecordForModuleWhenModuleHasNotBeenPersistedYet(): void
    {
        $faker = self::faker();

        $id = $faker->numberBetween(1);

        $module = Plugin\Domain\Module::create(
            Plugin\Domain\ModuleId::unknown(),
            Plugin\Domain\ModuleType::fromInt($faker->numberBetween(1)),
            Plugin\Domain\ModuleName::fromString($faker->sentence()),
            Plugin\Domain\ModuleTopic::fromString($faker->sentence()),
            Plugin\Domain\ModuleTarget::elementUrl(),
            Moodle\Domain\CourseId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\SectionId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
        );

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('insert_record')
            ->with(
                self::identicalTo(Plugin\Infrastructure\DatabaseBasedModuleRepository::TABLE),
                self::equalTo((object) [
                    'course' => $module->courseId()->toInt(),
                    'id' => $module->id()->toInt(),
                    'name' => $module->name()->toString(),
                    'section' => $module->sectionId()->toInt(),
                    'target' => $module->target()->toString(),
                    'timecreated' => $module->timecreated()->toInt(),
                    'timemodified' => $module->timemodified()->toInt(),
                    'topic' => $module->topic()->toString(),
                    'type' => $module->type()->toInt(),
                ]),
            )
            ->willReturn($id);

        $moduleRepository = new Plugin\Infrastructure\DatabaseBasedModuleRepository(
            $database,
            new Plugin\Infrastructure\ModuleNormalizer(),
        );

        $moduleRepository->save($module);

        self::assertSame($id, $module->id()->toInt());
    }

    public function testSaveUpdatesRecordForModuleWhenModuleHasBeenPersisted(): void
    {
        $faker = self::faker();

        $id = Plugin\Domain\ModuleId::fromInt($faker->numberBetween(1));

        $module = Plugin\Domain\Module::create(
            $id,
            Plugin\Domain\ModuleType::fromInt($faker->numberBetween(1)),
            Plugin\Domain\ModuleName::fromString($faker->sentence()),
            Plugin\Domain\ModuleTopic::fromString($faker->sentence()),
            Plugin\Domain\ModuleTarget::elementUrl(),
            Moodle\Domain\CourseId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\SectionId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
        );

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('update_record')
            ->with(
                self::identicalTo(Plugin\Infrastructure\DatabaseBasedModuleRepository::TABLE),
                self::equalTo((object) [
                    'course' => $module->courseId()->toInt(),
                    'id' => $module->id()->toInt(),
                    'name' => $module->name()->toString(),
                    'section' => $module->sectionId()->toInt(),
                    'target' => $module->target()->toString(),
                    'timecreated' => $module->timecreated()->toInt(),
                    'timemodified' => $module->timemodified()->toInt(),
                    'topic' => $module->topic()->toString(),
                    'type' => $module->type()->toInt(),
                ]),
            );

        $moduleRepository = new Plugin\Infrastructure\DatabaseBasedModuleRepository(
            $database,
            new Plugin\Infrastructure\ModuleNormalizer(),
        );

        $moduleRepository->save($module);

        self::assertSame($id, $module->id());
    }

    public function testRemoveRejectsModuleWhenModuleHasNotYetBeenPersisted(): void
    {
        $faker = self::faker();

        $module = Plugin\Domain\Module::create(
            Plugin\Domain\ModuleId::unknown(),
            Plugin\Domain\ModuleType::fromInt($faker->numberBetween(1)),
            Plugin\Domain\ModuleName::fromString($faker->sentence()),
            Plugin\Domain\ModuleTopic::fromString($faker->sentence()),
            Plugin\Domain\ModuleTarget::elementUrl(),
            Moodle\Domain\CourseId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\SectionId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
        );

        $database = $this->createMock(\moodle_database::class);

        $moduleRepository = new Plugin\Infrastructure\DatabaseBasedModuleRepository(
            $database,
            new Plugin\Infrastructure\ModuleNormalizer(),
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Can not remove module that has not yet been persisted.');

        $moduleRepository->remove($module);
    }

    public function testRemoveDeletesRecordForModuleWhenModuleHasBeenPersisted(): void
    {
        $faker = self::faker();

        $module = Plugin\Domain\Module::create(
            Plugin\Domain\ModuleId::fromInt($faker->numberBetween(1)),
            Plugin\Domain\ModuleType::fromInt($faker->numberBetween(1)),
            Plugin\Domain\ModuleName::fromString($faker->sentence()),
            Plugin\Domain\ModuleTopic::fromString($faker->sentence()),
            Plugin\Domain\ModuleTarget::elementUrl(),
            Moodle\Domain\CourseId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\SectionId::fromInt($faker->numberBetween(1)),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
            Moodle\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
        );

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('delete_records')
            ->with(
                self::identicalTo(Plugin\Infrastructure\DatabaseBasedModuleRepository::TABLE),
                self::identicalTo([
                    'id' => $module->id()->toInt(),
                ]),
            );

        $moduleRepository = new Plugin\Infrastructure\DatabaseBasedModuleRepository(
            $database,
            new Plugin\Infrastructure\ModuleNormalizer(),
        );

        $moduleRepository->remove($module);
    }
}
