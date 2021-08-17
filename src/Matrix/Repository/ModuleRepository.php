<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Matrix\Repository;

final class ModuleRepository
{
    private const TABLE = 'matrix';

    private $database;

    public function __construct(\moodle_database $database)
    {
        $this->database = $database;
    }

    public function findOneBy(array $conditions): ?object
    {
        $module = $this->database->get_record(
            self::TABLE,
            $conditions,
            '*',
            IGNORE_MISSING
        );

        if (!is_object($module)) {
            return null;
        }

        return $module;
    }

    /**
     * @return array<int, object>
     */
    public function findAllBy(array $conditions): array
    {
        return $this->database->get_records(
            self::TABLE,
            $conditions
        );
    }

    public function save(object $module): void
    {
        if (
            !property_exists($module, 'id')
            || !is_int($module->id)
        ) {
            $id = $this->database->insert_record(
                self::TABLE,
                $module
            );

            $module->id = $id;

            return;
        }

        $this->database->update_record(
            self::TABLE,
            $module
        );
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function remove(object $module): void
    {
        if (!property_exists($module, 'id')) {
            throw new \InvalidArgumentException('Can not remove module without identifier.');
        }

        $this->database->delete_records(
            self::TABLE,
            [
                'id' => $module->id,
            ]
        );
    }
}
