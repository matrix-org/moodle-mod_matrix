<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Plugin\Infrastructure;

use mod_matrix\Plugin;

final class DatabaseBasedModuleRepository implements Plugin\Domain\ModuleRepository
{
    public const TABLE = 'matrix';
    private $database;
    private $moduleNormalizer;

    public function __construct(
        \moodle_database $database,
        Plugin\Infrastructure\ModuleNormalizer $moduleNormalizer
    ) {
        $this->database = $database;
        $this->moduleNormalizer = $moduleNormalizer;
    }

    public function findOneBy(array $conditions): ?Plugin\Domain\Module
    {
        $module = $this->database->get_record(
            self::TABLE,
            $conditions,
            '*',
            IGNORE_MISSING,
        );

        if (!\is_object($module)) {
            return null;
        }

        return $this->moduleNormalizer->denormalize($module);
    }

    public function findAllBy(array $conditions): array
    {
        /** @var array<int, object> $modules */
        $modules = $this->database->get_records(
            self::TABLE,
            $conditions,
        );

        return \array_map(function (object $module): Plugin\Domain\Module {
            return $this->moduleNormalizer->denormalize($module);
        }, $modules);
    }

    public function save(Plugin\Domain\Module $module): void
    {
        if ($module->id()->equals(Plugin\Domain\ModuleId::unknown())) {
            $id = $this->database->insert_record(
                self::TABLE,
                $this->moduleNormalizer->normalize($module),
            );

            $reflection = new \ReflectionClass(Plugin\Domain\Module::class);

            $property = $reflection->getProperty('id');

            $property->setAccessible(true);
            $property->setValue(
                $module,
                Plugin\Domain\ModuleId::fromInt((int) $id),
            );

            return;
        }

        $this->database->update_record(
            self::TABLE,
            $this->moduleNormalizer->normalize($module),
        );
    }

    public function remove(Plugin\Domain\Module $module): void
    {
        if ($module->id()->equals(Plugin\Domain\ModuleId::unknown())) {
            throw new \InvalidArgumentException('Can not remove module that has not yet been persisted.');
        }

        $this->database->delete_records(
            self::TABLE,
            [
                'id' => $module->id()->toInt(),
            ],
        );
    }
}
