<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Moodle\Domain;

use mod_matrix\Moodle;

interface ModuleRepository
{
    public function findOneBy(array $conditions): ?Moodle\Domain\Module;

    /**
     * @return array<int, Moodle\Domain\Module>
     */
    public function findAllBy(array $conditions): array;

    public function save(Moodle\Domain\Module $module): void;

    /**
     * @throws \InvalidArgumentException
     */
    public function remove(Moodle\Domain\Module $module): void;
}
