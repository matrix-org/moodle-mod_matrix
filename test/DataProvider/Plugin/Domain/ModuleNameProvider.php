<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Test\DataProvider\Plugin\Domain;

use Ergebnis\DataProvider;
use mod_matrix\Plugin;

final class ModuleNameProvider extends DataProvider\AbstractProvider
{
    /**
     * @return \Generator<string, array{0: string}>
     */
    public function tooLong(): \Generator
    {
        $faker = self::faker();

        return self::provideDataForValues([
            'max-length-plus-1' => \str_pad(
                $faker->word(),
                Plugin\Domain\ModuleName::LENGTH_MAX + 1,
                $faker->randomLetter(),
            ),
            'max-length-plus-2' => \str_pad(
                $faker->word(),
                Plugin\Domain\ModuleName::LENGTH_MAX + 2,
                $faker->randomLetter(),
            ),
        ]);
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public function notTooLong(): \Generator
    {
        $faker = self::faker();

        return self::provideDataForValues([
            'max-length' => \str_pad(
                $faker->word(),
                Plugin\Domain\ModuleName::LENGTH_MAX,
                $faker->randomLetter(),
            ),
            'max-length-minus-1' => \str_pad(
                $faker->word(),
                Plugin\Domain\ModuleName::LENGTH_MAX - 1,
                $faker->randomLetter(),
            ),
        ]);
    }
}
