<?php

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
 * @covers \mod_matrix\Moodle\Domain\GroupId
 */
final class GroupIdTest extends Framework\TestCase
{
    use Util\Helper;

    /**
     * @dataProvider \Ergebnis\Test\Util\DataProvider\IntProvider::arbitrary()
     */
    public function testFromIntReturnsGroupId(int $value): void
    {
        $groupId = Moodle\Domain\GroupId::fromInt($value);

        self::assertSame($value, $groupId->toInt());
    }

    /**
     * @dataProvider \Ergebnis\Test\Util\DataProvider\IntProvider::arbitrary()
     */
    public function testFromStringReturnsGroupId(int $value): void
    {
        $groupId = Moodle\Domain\GroupId::fromString((string) $value);

        self::assertSame($value, $groupId->toInt());
    }

    public function testEqualsReturnsFalseWhenValueIsDifferent(): void
    {
        $faker = self::faker();

        $one = Moodle\Domain\GroupId::fromInt($faker->numberBetween(1));
        $two = Moodle\Domain\GroupId::fromInt($faker->numberBetween(1));

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenValueIsSame(): void
    {
        $value = self::faker()->numberBetween(1);

        $one = Moodle\Domain\GroupId::fromInt($value);
        $two = Moodle\Domain\GroupId::fromInt($value);

        self::assertTrue($one->equals($two));
    }
}
