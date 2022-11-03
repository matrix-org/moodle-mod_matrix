<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Test\DataProvider\Matrix\Domain;

use Ergebnis\DataProvider;

final class UsernameProvider extends DataProvider\AbstractProvider
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
            'username-with-at' => '@foo',
            'username-with-dot' => 'foo.bar',
            'username-with-umlaut' => 'foöbär',
            'username-with-slash-backward' => 'foo\bar',
            'username-with-slash-forward' => 'foo/bar',
            'username-with-space-leading' => ' foo',
            'username-with-space-middle' => 'foo bar',
            'username-with-space-trailing' => 'foo ',
            'username-with-upper-case-letters' => 'foO',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function validValues(): array
    {
        return [
            'username-with-digits-only' => '123',
            'username-with-letters-and-digits' => 'foo123',
            'username-with-letters-digits-and-dashes' => 'foo-123',
            'username-with-letters-digits-and-underscores' => 'foo_123',
            'username-with-letters-only' => 'foo',
        ];
    }
}
