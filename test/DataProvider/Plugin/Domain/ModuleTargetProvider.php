<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace mod_matrix\Test\DataProvider\Plugin\Domain;

use Ergebnis\DataProvider;
use mod_matrix\Plugin;

final class ModuleTargetProvider extends DataProvider\AbstractProvider
{
    /**
     * @return \Generator<string, array{0: string}>
     */
    public function known(): \Generator
    {
        $values = [
            Plugin\Domain\ModuleTarget::elementUrl()->toString(),
            Plugin\Domain\ModuleTarget::matrixTo()->toString(),
        ];

        return self::provideDataForValues(\array_combine(
            $values,
            $values,
        ));
    }
}
