<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Plugin\Domain;

use mod_matrix\Matrix;

interface MatrixUserIdLoader
{
    public function load(object $user): ?Matrix\Domain\UserId;
}
