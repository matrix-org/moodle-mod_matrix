<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Moodle\Domain;

final class GroupNotFound extends \RuntimeException
{
    public static function for(GroupId $groupId): self
    {
        return new self(\sprintf(
            'Could not find group with id %d.',
            $groupId->toInt(),
        ));
    }
}
