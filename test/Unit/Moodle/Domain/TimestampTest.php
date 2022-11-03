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
 * @covers \mod_matrix\Moodle\Domain\Timestamp
 */
final class TimestampTest extends Framework\TestCase
{
    /**
     * @dataProvider \Ergebnis\DataProvider\IntProvider::arbitrary()
     */
    public function testFromIntReturnsTimestamp(int $value): void
    {
        $timestamp = Moodle\Domain\Timestamp::fromInt($value);

        self::assertSame($value, $timestamp->toInt());
    }

    /**
     * @dataProvider \Ergebnis\DataProvider\IntProvider::arbitrary()
     */
    public function testFromStringReturnsTimestamp(int $value): void
    {
        $timestamp = Moodle\Domain\Timestamp::fromString((string) $value);

        self::assertSame($value, $timestamp->toInt());
    }
}
