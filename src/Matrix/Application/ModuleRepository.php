<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Matrix\Application;

use mod_matrix\Matrix;

interface ModuleRepository
{
    public function findOneBy(array $conditions): ?Matrix\Domain\Module;

    /**
     * @return array<int, Matrix\Domain\Module>
     */
    public function findAllBy(array $conditions): array;

    public function save(Matrix\Domain\Module $module): void;

    /**
     * @throws \InvalidArgumentException
     */
    public function remove(Matrix\Domain\Module $module): void;
}
