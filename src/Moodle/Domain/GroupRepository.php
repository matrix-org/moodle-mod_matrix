<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Moodle\Domain;

interface GroupRepository
{
    public function find(GroupId $groupId): ?Group;
}
