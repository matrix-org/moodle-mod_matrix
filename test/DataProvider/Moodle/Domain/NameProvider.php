<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\DataProvider\Moodle\Domain;

use Ergebnis\DataProvider;
use mod_matrix\Moodle;

final class NameProvider extends DataProvider\AbstractProvider
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
                Moodle\Domain\Name::LENGTH_MAX + 1,
                $faker->randomLetter(),
            ),
            'max-length-plus-2' => \str_pad(
                $faker->word(),
                Moodle\Domain\Name::LENGTH_MAX + 2,
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
                Moodle\Domain\Name::LENGTH_MAX,
                $faker->randomLetter(),
            ),
            'max-length-minus-1' => \str_pad(
                $faker->word(),
                Moodle\Domain\Name::LENGTH_MAX - 1,
                $faker->randomLetter(),
            ),
        ]);
    }
}
