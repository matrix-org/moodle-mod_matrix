<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Matrix\Repository;

use Ergebnis\Test\Util;
use mod_matrix\Matrix;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Matrix\Repository\ModuleRepository
 */
final class ModuleRepositoryTest extends Framework\TestCase
{
    use Util\Helper;

    public function testFindOneByReturnsNullWhenModuleCouldNotBeFound(): void
    {
        $faker = self::faker()->unique();

        $conditions = [
            'course_id' => $faker->numberBetween(1),
            'group_id' => $faker->numberBetween(1),
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

        $moduleRepository = new Matrix\Repository\ModuleRepository($database);

        self::assertNull($moduleRepository->findOneBy($conditions));
    }

    public function testFindOneByReturnsModuleWhenModuleCouldBeFound(): void
    {
        $faker = self::faker();

        $courseId = $faker->numberBetween(1);
        $groupId = $faker->numberBetween(1);

        $conditions = [
            'course_id' => $courseId,
            'group_id' => $groupId,
        ];

        $module = (object) [
            'course_id' => $courseId,
            'group_id' => $groupId,
            'id' => $faker->numberBetween(1),
            'module_id' => $faker->sha1(),
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
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
            ->willReturn($module);

        $moduleRepository = new Matrix\Repository\ModuleRepository($database);

        self::assertSame($module, $moduleRepository->findOneBy($conditions));
    }

    public function testFindAllByReturnsModulesForConditions(): void
    {
        $faker = self::faker();

        $courseId = $faker->numberBetween(1);

        $conditions = [
            'course' => $courseId,
        ];

        $modules = [
            (object) [
                'course' => $courseId,
                'name' => $faker->sentence(),
                'timecreated' => $faker->dateTime()->getTimestamp(),
                'timemodified' => $faker->dateTime()->getTimestamp(),
                'type' => $faker->numberBetween(1),
            ],
            (object) [
                'course' => $courseId,
                'name' => $faker->sentence(),
                'timecreated' => $faker->dateTime()->getTimestamp(),
                'timemodified' => $faker->dateTime()->getTimestamp(),
                'type' => $faker->numberBetween(1),
            ],
        ];

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('get_records')
            ->with(
                self::identicalTo('matrix'),
                self::identicalTo($conditions)
            )
            ->willReturn($modules);

        $moduleRepository = new Matrix\Repository\ModuleRepository($database);

        self::assertSame($modules, $moduleRepository->findAllBy($conditions));
    }

    public function testSaveInsertsRecordForModuleWhenModuleDoesNotHaveId(): void
    {
        $faker = self::faker();

        $id = $faker->numberBetween(1);

        $module = (object) [
            'course' => $faker->numberBetween(1),
            'name' => $faker->sentence(),
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
            'type' => $faker->numberBetween(1),
        ];

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('insert_record')
            ->with(
                self::identicalTo('matrix'),
                self::identicalTo($module)
            )
            ->willReturn($id);

        $moduleRepository = new Matrix\Repository\ModuleRepository($database);

        $moduleRepository->save($module);

        self::assertSame($id, $module->id);
    }

    public function testSaveInsertsRecordForModuleWhenModuleHasIdButItIsNull(): void
    {
        $faker = self::faker();

        $id = $faker->numberBetween(1);

        $module = (object) [
            'course' => $faker->numberBetween(1),
            'id' => null,
            'name' => $faker->sentence(),
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
            'type' => $faker->numberBetween(1),
        ];

        $database = $this->createMock(\moodle_database::class);

        $database
            ->expects(self::once())
            ->method('insert_record')
            ->with(
                self::identicalTo('matrix'),
                self::identicalTo($module)
            )
            ->willReturn($id);

        $moduleRepository = new Matrix\Repository\ModuleRepository($database);

        $moduleRepository->save($module);

        self::assertSame($id, $module->id);
    }

    public function testSaveUpdatesRecordForModuleWhenModuleHasId(): void
    {
        $faker = self::faker();

        $id = $faker->numberBetween(1);

        $module = (object) [
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
            ->method('update_record')
            ->with(
                self::identicalTo('matrix'),
                self::identicalTo($module)
            );

        $moduleRepository = new Matrix\Repository\ModuleRepository($database);

        $moduleRepository->save($module);

        self::assertSame($id, $module->id);
    }

    public function testRemoveRejectsModuleWhenModuleDoesNotHaveId(): void
    {
        $faker = self::faker();

        $module = (object) [
            'course' => $faker->numberBetween(1),
            'name' => $faker->sentence(),
            'timecreated' => $faker->dateTime()->getTimestamp(),
            'timemodified' => $faker->dateTime()->getTimestamp(),
            'type' => $faker->numberBetween(1),
        ];

        $database = $this->createMock(\moodle_database::class);

        $moduleRepository = new Matrix\Repository\ModuleRepository($database);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Can not remove module without identifier.');

        $moduleRepository->remove($module);
    }

    public function testRemoveDeletesRecordForModuleWhenModuleHasId(): void
    {
        $faker = self::faker();

        $id = $faker->numberBetween(1);

        $module = (object) [
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
            ->method('delete_records')
            ->with(
                self::identicalTo('matrix'),
                self::identicalTo([
                    'id' => $id,
                ])
            );

        $moduleRepository = new Matrix\Repository\ModuleRepository($database);

        $moduleRepository->remove($module);
    }
}
