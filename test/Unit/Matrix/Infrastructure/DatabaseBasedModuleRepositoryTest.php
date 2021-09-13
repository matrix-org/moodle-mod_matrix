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
use const IGNORE_MISSING;

/**
 * @internal
 *
 * @covers \mod_matrix\Matrix\Infrastructure\DatabaseBasedModuleRepository
 *
 * @uses \mod_matrix\Matrix\Domain\CourseId
 * @uses \mod_matrix\Matrix\Domain\Module
 * @uses \mod_matrix\Matrix\Domain\ModuleId
 * @uses \mod_matrix\Matrix\Domain\Name
 * @uses \mod_matrix\Matrix\Domain\Timestamp
 * @uses \mod_matrix\Matrix\Domain\Type
 * @uses \mod_matrix\Matrix\Infrastructure\ModuleNormalizer
 */
final class DatabaseBasedModuleRepositoryTest extends Framework\TestCase
{
    use Util\Helper;

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
                self::identicalTo('matrix'),
                self::identicalTo($conditions),
                self::identicalTo('*'),
                self::identicalTo(IGNORE_MISSING)
            )
            ->willReturn(null);

        $moduleRepository = new Matrix\Infrastructure\DatabaseBasedModuleRepository(
            $database,
            new Matrix\Infrastructure\ModuleNormalizer()
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
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
            'type' => $faker->numberBetween(1),
        ];

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('get_record')
            ->with(
                self::identicalTo('matrix'),
                self::identicalTo($conditions),
                self::identicalTo('*'),
                self::identicalTo(IGNORE_MISSING)
            )
            ->willReturn($normalized);

        $moduleRepository = new Matrix\Infrastructure\DatabaseBasedModuleRepository(
            $database,
            new Matrix\Infrastructure\ModuleNormalizer()
        );

        $expected = Matrix\Domain\Module::create(
            Matrix\Domain\ModuleId::fromString((string) $normalized->id),
            Matrix\Domain\Type::fromString((string) $normalized->type),
            Matrix\Domain\Name::fromString((string) $normalized->name),
            Matrix\Domain\CourseId::fromString((string) $normalized->course),
            Matrix\Domain\Timestamp::fromString((string) $normalized->timecreated),
            Matrix\Domain\Timestamp::fromString((string) $normalized->timemodified)
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
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
            'type' => $faker->numberBetween(1),
        ];

        $two = (object) [
            'course' => $courseId,
            'id' => $faker->numberBetween(1),
            'name' => $faker->sentence(),
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
            'type' => $faker->numberBetween(1),
        ];

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('get_records')
            ->with(
                self::identicalTo('matrix'),
                self::identicalTo($conditions)
            )
            ->willReturn([
                $one,
                $two,
            ]);

        $moduleRepository = new Matrix\Infrastructure\DatabaseBasedModuleRepository(
            $database,
            new Matrix\Infrastructure\ModuleNormalizer()
        );

        $expected = [
            Matrix\Domain\Module::create(
                Matrix\Domain\ModuleId::fromString((string) $one->id),
                Matrix\Domain\Type::fromString((string) $one->type),
                Matrix\Domain\Name::fromString((string) $one->name),
                Matrix\Domain\CourseId::fromString((string) $one->course),
                Matrix\Domain\Timestamp::fromString((string) $one->timecreated),
                Matrix\Domain\Timestamp::fromString((string) $one->timemodified)
            ),
            Matrix\Domain\Module::create(
                Matrix\Domain\ModuleId::fromString((string) $two->id),
                Matrix\Domain\Type::fromString((string) $two->type),
                Matrix\Domain\Name::fromString((string) $two->name),
                Matrix\Domain\CourseId::fromString((string) $two->course),
                Matrix\Domain\Timestamp::fromString((string) $two->timecreated),
                Matrix\Domain\Timestamp::fromString((string) $two->timemodified)
            ),
        ];

        self::assertEquals($expected, $moduleRepository->findAllBy($conditions));
    }

    public function testSaveInsertsRecordForModuleWhenModuleHasNotBeenPersistedYet(): void
    {
        $faker = self::faker();

        $id = $faker->numberBetween(1);

        $module = Matrix\Domain\Module::create(
            Matrix\Domain\ModuleId::unknown(),
            Matrix\Domain\Type::fromInt($faker->numberBetween(1)),
            Matrix\Domain\Name::fromString($faker->sentence()),
            Matrix\Domain\CourseId::fromInt($faker->numberBetween(1)),
            Matrix\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
            Matrix\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp())
        );

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('insert_record')
            ->with(
                self::identicalTo('matrix'),
                self::equalTo((object) [
                    'course' => $module->courseId()->toInt(),
                    'id' => $module->id()->toInt(),
                    'name' => $module->name()->toString(),
                    'timecreated' => $module->timecreated()->toInt(),
                    'timemodified' => $module->timemodified()->toInt(),
                    'type' => $module->type()->toInt(),
                ])
            )
            ->willReturn($id);

        $moduleRepository = new Matrix\Infrastructure\DatabaseBasedModuleRepository(
            $database,
            new Matrix\Infrastructure\ModuleNormalizer()
        );

        $moduleRepository->save($module);

        self::assertSame($id, $module->id()->toInt());
    }

    public function testSaveUpdatesRecordForModuleWhenModuleHasBeenPersisted(): void
    {
        $faker = self::faker();

        $id = Matrix\Domain\ModuleId::fromInt($faker->numberBetween(1));

        $module = Matrix\Domain\Module::create(
            $id,
            Matrix\Domain\Type::fromInt($faker->numberBetween(1)),
            Matrix\Domain\Name::fromString($faker->sentence()),
            Matrix\Domain\CourseId::fromInt($faker->numberBetween(1)),
            Matrix\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
            Matrix\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp())
        );

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('update_record')
            ->with(
                self::identicalTo('matrix'),
                self::equalTo((object) [
                    'course' => $module->courseId()->toInt(),
                    'id' => $module->id()->toInt(),
                    'name' => $module->name()->toString(),
                    'timecreated' => $module->timecreated()->toInt(),
                    'timemodified' => $module->timemodified()->toInt(),
                    'type' => $module->type()->toInt(),
                ])
            );

        $moduleRepository = new Matrix\Infrastructure\DatabaseBasedModuleRepository(
            $database,
            new Matrix\Infrastructure\ModuleNormalizer()
        );

        $moduleRepository->save($module);

        self::assertSame($id, $module->id());
    }

    public function testRemoveRejectsModuleWhenModuleHasNotYetBeenPersisted(): void
    {
        $faker = self::faker();

        $module = Matrix\Domain\Module::create(
            Matrix\Domain\ModuleId::unknown(),
            Matrix\Domain\Type::fromInt($faker->numberBetween(1)),
            Matrix\Domain\Name::fromString($faker->sentence()),
            Matrix\Domain\CourseId::fromInt($faker->numberBetween(1)),
            Matrix\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
            Matrix\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp())
        );

        $database = $this->createMock(\moodle_database::class);

        $moduleRepository = new Matrix\Infrastructure\DatabaseBasedModuleRepository(
            $database,
            new Matrix\Infrastructure\ModuleNormalizer()
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Can not remove module that has not yet been persisted.');

        $moduleRepository->remove($module);
    }

    public function testRemoveDeletesRecordForModuleWhenModuleHasBeenPersisted(): void
    {
        $faker = self::faker();

        $module = Matrix\Domain\Module::create(
            Matrix\Domain\ModuleId::fromInt($faker->numberBetween(1)),
            Matrix\Domain\Type::fromInt($faker->numberBetween(1)),
            Matrix\Domain\Name::fromString($faker->sentence()),
            Matrix\Domain\CourseId::fromInt($faker->numberBetween(1)),
            Matrix\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp()),
            Matrix\Domain\Timestamp::fromInt($faker->dateTime()->getTimestamp())
        );

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('delete_records')
            ->with(
                self::identicalTo('matrix'),
                self::identicalTo([
                    'id' => $module->id()->toInt(),
                ])
            );

        $moduleRepository = new Matrix\Infrastructure\DatabaseBasedModuleRepository(
            $database,
            new Matrix\Infrastructure\ModuleNormalizer()
        );

        $moduleRepository->remove($module);
    }
}
