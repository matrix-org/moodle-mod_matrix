<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Plugin\Domain;

use mod_matrix\Plugin;
use mod_matrix\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Plugin\Domain\RoomId
 */
final class RoomIdTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider \Ergebnis\DataProvider\IntProvider::arbitrary()
     */
    public function testFromIntReturnsRoomId(int $value): void
    {
        $roomId = Plugin\Domain\RoomId::fromInt($value);

        self::assertSame($value, $roomId->toInt());
    }

    /**
     * @dataProvider \Ergebnis\DataProvider\IntProvider::arbitrary()
     */
    public function testFromStringReturnsRoomId(int $value): void
    {
        $roomId = Plugin\Domain\RoomId::fromString((string) $value);

        self::assertSame($value, $roomId->toInt());
    }

    public function testUnknownReturnsRoomId(): void
    {
        $roomId = Plugin\Domain\RoomId::unknown();

        self::assertSame(-1, $roomId->toInt());
    }

    public function testEqualsReturnsFalseWhenValueIsDifferent(): void
    {
        $faker = self::faker();

        $one = Plugin\Domain\RoomId::fromInt($faker->numberBetween(1));
        $two = Plugin\Domain\RoomId::fromInt($faker->numberBetween(1));

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValueIsSame(): void
    {
        $value = self::faker()->numberBetween(1);

        $one = Plugin\Domain\RoomId::fromInt($value);
        $two = Plugin\Domain\RoomId::fromInt($value);

        self::assertTrue($one->equals($two));
    }
}
