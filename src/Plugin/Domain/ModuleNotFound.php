<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Plugin\Domain;

final class ModuleNotFound extends \RuntimeException
{
    public static function for(ModuleId $moduleId): self
    {
        return new self(\sprintf(
            'Could not find module with id %d.',
            $moduleId->toInt(),
        ));
    }
}
