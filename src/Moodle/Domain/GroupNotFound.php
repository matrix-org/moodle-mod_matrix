<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
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
