<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Moodle\Domain;

/**
 * @psalm-immutable
 */
final class Group
{
    private $id;
    private $name;

    private function __construct(
        GroupId $id,
        GroupName $name
    ) {
        $this->id = $id;
        $this->name = $name;
    }

    public static function create(
        GroupId $id,
        GroupName $name
    ): self {
        return new self(
            $id,
            $name,
        );
    }

    public function id(): GroupId
    {
        return $this->id;
    }

    public function name(): GroupName
    {
        return $this->name;
    }
}
