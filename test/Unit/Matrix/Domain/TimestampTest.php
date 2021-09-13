<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Matrix\Domain;

use mod_matrix\Matrix;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Matrix\Domain\Timestamp
 */
final class TimestampTest extends Framework\TestCase
{
    /**
     * @dataProvider \Ergebnis\Test\Util\DataProvider\IntProvider::arbitrary()
     */
    public function testFromIntReturnsTimestamp(int $value): void
    {
        $timestamp = Matrix\Domain\Timestamp::fromInt($value);

        self::assertSame($value, $timestamp->toInt());
    }

    /**
     * @dataProvider \Ergebnis\Test\Util\DataProvider\IntProvider::arbitrary()
     */
    public function testFromStringReturnsTimestamp(int $value): void
    {
        $timestamp = Matrix\Domain\Timestamp::fromString((string) $value);

        self::assertSame($value, $timestamp->toInt());
    }
}