<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Test\DataProvider\Matrix\Domain;

use Ergebnis\DataProvider;

final class HomeserverProvider extends DataProvider\AbstractProvider
{
    /**
     * @return \Generator<string, array{0: string}>
     */
    public function invalid(): \Generator
    {
        return self::provideDataForValues(self::invalidValues());
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public function valid(): \Generator
    {
        return self::provideDataForValues(self::validValues());
    }

    /**
     * @return array<string, string>
     */
    public static function invalidValues(): array
    {
        return [
            'home-server-without-tld' => 'example',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function validValues(): array
    {
        return [
            'home-server-with-tld-with-2-letters' => 'example.de',
            'home-server-with-tld-with-2-segments' => 'example.co.uk',
            'home-server-with-tld-with-3-letters' => 'example.org',
        ];
    }
}
