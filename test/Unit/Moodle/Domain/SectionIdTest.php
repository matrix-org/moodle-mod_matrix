<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2022, New Vector Ltd (Trading as Element)
 * @license   SPDX-License-Identifier: Apache-2.0
 */

namespace mod_matrix\Test\Unit\Moodle\Domain;

use mod_matrix\Moodle;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Moodle\Domain\SectionId
 */
final class SectionIdTest extends Framework\TestCase
{
    /**
     * @dataProvider \Ergebnis\DataProvider\IntProvider::arbitrary()
     */
    public function testFromIntReturnsSectionId(int $value): void
    {
        $sectionId = Moodle\Domain\SectionId::fromInt($value);

        self::assertSame($value, $sectionId->toInt());
    }

    /**
     * @dataProvider \Ergebnis\DataProvider\IntProvider::arbitrary()
     */
    public function testFromStringReturnsSectionId(int $value): void
    {
        $sectionId = Moodle\Domain\SectionId::fromString((string) $value);

        self::assertSame($value, $sectionId->toInt());
    }
}
