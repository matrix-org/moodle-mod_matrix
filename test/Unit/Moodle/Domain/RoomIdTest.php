<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Test\Unit\Moodle\Domain;

use Ergebnis\Test\Util;
use mod_matrix\Moodle;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \mod_matrix\Moodle\Domain\RoomId
 */
final class RoomIdTest extends Framework\TestCase
{
    use Util\Helper;

    /**
     * @dataProvider \Ergebnis\Test\Util\DataProvider\IntProvider::arbitrary()
     */
    public function testFromIntReturnsRoomId(int $value): void
    {
        $roomId = Moodle\Domain\RoomId::fromInt($value);

        self::assertSame($value, $roomId->toInt());
    }

    /**
     * @dataProvider \Ergebnis\Test\Util\DataProvider\IntProvider::arbitrary()
     */
    public function testFromStringReturnsRoomId(int $value): void
    {
        $roomId = Moodle\Domain\RoomId::fromString((string) $value);

        self::assertSame($value, $roomId->toInt());
    }

    public function testUnknownReturnsRoomId(): void
    {
        $roomId = Moodle\Domain\RoomId::unknown();

        self::assertSame(-1, $roomId->toInt());
    }

    public function testEqualsReturnsFalseWhenValueIsDifferent(): void
    {
        $faker = self::faker();

        $one = Moodle\Domain\RoomId::fromInt($faker->numberBetween(1));
        $two = Moodle\Domain\RoomId::fromInt($faker->numberBetween(1));

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValueIsSame(): void
    {
        $value = self::faker()->numberBetween(1);

        $one = Moodle\Domain\RoomId::fromInt($value);
        $two = Moodle\Domain\RoomId::fromInt($value);

        self::assertTrue($one->equals($two));
    }
}
